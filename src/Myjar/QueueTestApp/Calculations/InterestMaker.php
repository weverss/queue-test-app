<?php

namespace Myjar\QueueTestApp\Calculations;

class InterestMaker
{
    public static function create($interestMessage)
    {
        $interestData = json_decode($interestMessage, true);

        if (!isset($interestData['sum']) || !isset($interestData['days'])) {
            throw new Exception("Invalid interest message $interestMessage!");
        }

        $interest = new Interest();

        $interest->sum = $interestData['sum'];
        $interest->days = $interestData['days'];

        return $interest;
    }
}
