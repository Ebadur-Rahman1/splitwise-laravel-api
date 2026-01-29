<?php

namespace App\Services;

class SplitCalculator
{
    /**
     * @param float $totalAmount
     * @param int $memberCount
     * @return array
     */
    public function splitEqually(float $totalAmount, int $memberCount): array
    {
        if ($memberCount <= 0) {
            throw new \InvalidArgumentException("Member count must be greater than 0");
        }

        // Base split rounded to 2 decimals
        $baseSplit = round($totalAmount / $memberCount, 2);

        $splits = array_fill(0, $memberCount, $baseSplit);

        // Fix rounding difference
        $currentTotal = array_sum($splits);
        $difference = round($totalAmount - $currentTotal, 2);

        // Add/subtract difference to first member
        $splits[0] = round($splits[0] + $difference, 2);

        return $splits;
    }
}
