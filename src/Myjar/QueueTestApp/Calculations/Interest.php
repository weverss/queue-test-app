<?php

namespace Myjar\QueueTestApp\Calculations;

class Interest
{
    const DIVISIBLE_BY_THREE_INTEREST_RATE = 1;
    const DIVISIBLE_BY_FIVE_INTEREST_RATE = 2;
    const DIVISIBLE_BY_BOTH_THREE_AND_FIVE_INTEREST_RATE = 3;
    const NOT_DIVISIBLE_BY_EITHER_THREE_OR_FIVE_INTEREST_RATE = 4;

    const INTEREST_DECIMAL_DIGITS = 2;

    public $sum;
    public $days;
    public $interest;
    public $totalSum;

    public function solve()
    {
        $this->interest = $this->calculateInterest();
        $this->totalSum = $this->calculateTotalSum();

        return $this;
    }

    protected function calculateInterest()
    {
        $interest = 0;

        if (!$this->days) {
            return $interest;
        }

        for ($day = 1; $day <= $this->days; $day++) {
            $interest += $this->calculateDayInterest($day);
        }

        return $interest;
    }

    protected function calculateDayInterest($day)
    {
        $interestRate = $this->getDayInterestRate($day);
        $interest = $this->sum * $interestRate / 100;

        return round($interest, $this::INTEREST_DECIMAL_DIGITS);
    }

    protected function getDayInterestRate($day)
    {
        if ($this->isDayDivisibleByThree($day) && $this->isDayDivisibleByFive($day)) {
            return $this::DIVISIBLE_BY_BOTH_THREE_AND_FIVE_INTEREST_RATE;
        }

        if ($this->isDayDivisibleByThree($day)) {
            return $this::DIVISIBLE_BY_THREE_INTEREST_RATE;
        }

        if ($this->isDayDivisibleByFive($day)) {
            return $this::DIVISIBLE_BY_FIVE_INTEREST_RATE;
        }

        return $this::NOT_DIVISIBLE_BY_EITHER_THREE_OR_FIVE_INTEREST_RATE;
    }

    protected function isDayDivisibleByThree($day)
    {
        return $day % 3 === 0;
    }

    protected function isDayDivisibleByFive($day)
    {
        return $day % 5 === 0;
    }

    protected function calculateTotalSum()
    {
        return $this->interest + $this->sum;
    }

    public function getInterestData()
    {
        return [
            'sum' => $this->sum,
            'days' => $this->days,
            'interest' => $this->interest,
            'totalSum' => $this->totalSum,
        ];
    }
}
