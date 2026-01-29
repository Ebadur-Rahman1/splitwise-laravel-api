<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DebtAggregator;

class DebtAggregatorTest extends TestCase
{
    public function test_it_aggregates_who_owes_whom_correctly()
    {
        $aggregator = new DebtAggregator();

        $splits = [
            // User 2 owes user 1 => 500
            [
                'paid_by' => 1,
                'user_id' => 2,
                'amount' => 500,
                'is_settled' => false
            ],
            // User 3 owes user 1 => 300
            [
                'paid_by' => 1,
                'user_id' => 3,
                'amount' => 300,
                'is_settled' => false
            ],
            // User 2 owes user 1 => 200 more
            [
                'paid_by' => 1,
                'user_id' => 2,
                'amount' => 200,
                'is_settled' => false
            ],
            // Settled, should be ignored
            [
                'paid_by' => 4,
                'user_id' => 1,
                'amount' => 100,
                'is_settled' => true
            ],
            // Paid for himself, should be ignored
            [
                'paid_by' => 5,
                'user_id' => 5,
                'amount' => 1000,
                'is_settled' => false
            ],
        ];

        $result = $aggregator->aggregate($splits);

        // Assertions
        $this->assertArrayHasKey(2, $result);
        $this->assertArrayHasKey(3, $result);

        $this->assertEquals(700, $result[2][1]); // 500 + 200
        $this->assertEquals(300, $result[3][1]);

        // Ensure no self debts
        $this->assertArrayNotHasKey(5, $result);
    }
}
