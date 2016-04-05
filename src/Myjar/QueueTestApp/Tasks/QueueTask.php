<?php

namespace Myjar\QueueTestApp\Tasks;

use DateTime;
use Myjar\QueueTestApp\Calculations\InterestMaker;
use Myjar\QueueTestApp\Calculations\Interest;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueTask
{
    const INTEREST_QUEUE = 'interest-queue';
    const SOLVED_INTEREST_QUEUE = 'solved-interest-queue';

    const LOG_TYPE_RECEIVED = 'received';
    const LOG_TYPE_PUBLISHED = 'published';

    const DELIVERY_MODE_PERSITENT = 2;

    protected $host = 'impact.ccat.eu';
    protected $port = '5672';
    protected $user = 'myjar';
    protected $password = 'myjar';

    protected $channel;
    protected $connection;

    protected $token = 'wevers';

    public function run()
    {
        $this->channel()->basic_consume($this::INTEREST_QUEUE, $this->token, null, null, null, null, function ($msg) {
            $interest = InterestMaker::create($msg->body);

            if (!$interest->isValid()) {
                return;
            }

            $this->log($this::LOG_TYPE_RECEIVED, $msg->body);

            $interest->solve();
            $this->publishSolvedInterest($interest);
        });

        while ($this->channel()->callbacks) {
            $this->channel()->wait();
        }
    }

    protected function publishSolvedInterest(Interest $interest)
    {
        $data = $interest->getInterestData();
        $data['token'] = $this->token;

        $amqpMessage = $this->createAmqpMessage($data);
        $this->channel()->basic_publish($amqpMessage, null, $this::SOLVED_INTEREST_QUEUE);

        $this->log($this::LOG_TYPE_PUBLISHED, $amqpMessage->body);
    }

    protected function createAmqpMessage(array $data)
    {
        $body = json_encode($data);

        $options = [
            'content_type' => 'text/json',
            'delivery_mode' => $this::DELIVERY_MODE_PERSITENT
        ];

        return new AMQPMessage($body, $options);
    }

    protected function log($type, $message)
    {
        $dateTime = new DateTime();
        $dateTimeString = $dateTime->format('Y-m-d H:i:s');

        printf("%s: %s %s\n", $dateTimeString, $type, $message);
    }

    protected function channel()
    {
        if (!$this->channel) {
            $this->channel = $this->connection()->channel();
        }

        return $this->channel;
    }

    protected function connection()
    {
        if (!$this->connection) {
            $this->connection = new AMQPConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password
            );
        }

        return $this->connection;
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }

        if ($this->connection) {
            $this->connection->close();
        }
    }
}
