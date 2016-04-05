<?php

namespace Myjar\QueueTestApp\Calculations;

class InterestMaker
{
    public static function create($interestMessage)
    {
        $interest = new Interest();
        $interestData = json_decode($interestMessage, true);

        if (isset($interestData['sum'])) {
            $interest->sum = floatval($interestData['sum']);
        }

        if (isset($interestData['days'])) {
            $interest->days = intval($interestData['days']);
        }

        return $interest;
    }
}
