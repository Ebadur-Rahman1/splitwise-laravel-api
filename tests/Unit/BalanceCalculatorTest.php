<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\BalanceCalculator;

class BalanceCalculatorTest extends TestCase
{
    public function test_it_calculates_balances_correctly()
    {
        $calculator = new BalanceCalculator();

        $splits = [
            // I owe someone 500
            [
                'user_id' => 1,
                'paid_by' => 2,
                'amount' => 500,
                'is_settled' => false
            ],
            // Someone owes me 300
            [
                'user_id' => 3,
                'paid_by' => 1,
                'amount' => 300,
                'is_settled' => false
            ],
            // Already settled, should be ignored
            [
                'user_id' => 1,
                'paid_by' => 4,
                'amount' => 200,
                'is_settled' => true
            ],
        ];

        $result = $calculator->calculate(1, $splits);

        $this->assertEquals(500, $result['you_owe']);
        $this->assertEquals(300, $result['owed_to_you']);
    }
}
