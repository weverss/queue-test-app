<?php

namespace Myjar\QueueTestApp\Calculations;

class Interest
{
    const DIVISIBLE_BY_THREE = 3;
    const DIVISIBLE_BY_FIVE = 5;

    const DIVISIBLE_BY_THREE_INTEREST_RATE = 1;
    const DIVISIBLE_BY_FIVE_INTEREST_RATE = 2;
    const DIVISIBLE_BY_BOTH_THREE_AND_FIVE_INTEREST_RATE = 3;
    const NOT_DIVISIBLE_BY_EITHER_THREE_OR_FIVE_INTEREST_RATE = 4;

    const INTEREST_DECIMAL_DIGITS = 2;

    public $sum;
    public $days;
    public $interest;
    public $totalSum;

    public function isValid()
    {
        return $this->sum > 0 && $this->days > 0;
    }

    public function solve()
    {
        $this->interest = floatval($this->calculateInterest());
        $this->totalSum = floatval($this->calculateTotalSum());

        return $this;
    }

    protected function calculateInterest()
    {
        $interest = $this->getDayInterest($this::NOT_DIVISIBLE_BY_EITHER_THREE_OR_FIVE_INTEREST_RATE)
            * $this->getDaysNotDivisibleByBothThreeAndFive();

        $interest += $this->getDayInterest($this::DIVISIBLE_BY_THREE_INTEREST_RATE)
            * $this->getDaysDivisibleByOnlyThree();

        $interest += $this->getDayInterest($this::DIVISIBLE_BY_FIVE_INTEREST_RATE)
            * $this->getDaysDivisibleByOnlyFive();

        $interest += $this->getDayInterest($this::DIVISIBLE_BY_BOTH_THREE_AND_FIVE_INTEREST_RATE)
            * $this->getDaysDivisibleByBothThreeAndFive();

        return $interest;
    }

    protected function getDayInterest($interestRate)
    {
        $interest = $this->sum * $interestRate / 100;

        return round($interest, $this::INTEREST_DECIMAL_DIGITS);
    }

    protected function getDaysNotDivisibleByBothThreeAndFive()
    {
        return $this->days
            - $this->getDaysDivisibleByBothThreeAndFive()
            - $this->getDaysDivisibleByOnlyThree()
            - $this->getDaysDivisibleByOnlyFive();
    }

    protected function getDaysDivisibleByOnlyThree()
    {
        return $this->getDaysDivisibleBy($this::DIVISIBLE_BY_THREE) - $this->getDaysDivisibleByBothThreeAndFive();
    }

    protected function getDaysDivisibleByOnlyFive()
    {
        return $this->getDaysDivisibleBy($this::DIVISIBLE_BY_FIVE) - $this->getDaysDivisibleByBothThreeAndFive();
    }

    protected function getDaysDivisibleByBothThreeAndFive()
    {
        $greatestCommonDivisor = $this::DIVISIBLE_BY_THREE * $this::DIVISIBLE_BY_FIVE;

        return $this->getDaysDivisibleBy($greatestCommonDivisor);
    }


    protected function calculateDayInterest($rate)
    {
        $interest = $this->sum * $rate / 100;

        return round($interest, $this::INTEREST_DECIMAL_DIGITS);
    }

    protected function getDaysDivisibleBy($divisor)
    {
        if (!$this->hasAnyDayDivisibleBy($divisor)) {
            return 0;
        }

        return $this->getHighestDivisibleDay($divisor) / $divisor;
    }

    protected function hasAnyDayDivisibleBy($divisor)
    {
        if (abs($this->days) >= $divisor) {
            return true;
        }

        return false;
    }

    protected function getHighestDivisibleDay($divisor)
    {
        $day = abs($this->days);

        while ($day % $divisor) {
            $day--;
        }

        return $day;
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
