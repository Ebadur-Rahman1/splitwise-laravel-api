<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\SettlementService;

class SettlementServiceTest extends TestCase
{
    public function test_it_settles_only_given_users_splits()
    {
        $service = new SettlementService();

        $splits = [
            [
                'id' => 1,
                'user_id' => 1,
                'amount' => 500,
                'is_settled' => false
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'amount' => 300,
                'is_settled' => false
            ],
            [
                'id' => 3,
                'user_id' => 2,
                'amount' => 200,
                'is_settled' => false
            ],
            [
                'id' => 4,
                'user_id' => 1,
                'amount' => 100,
                'is_settled' => true // already settled
            ],
        ];

        $result = $service->settleUserSplits(1, $splits);

        // User 1 splits should be settled
        $this->assertTrue($result[0]['is_settled']);
        $this->assertTrue($result[1]['is_settled']);
        $this->assertTrue($result[3]['is_settled']); // already true

        // Other user's split should remain unchanged
        $this->assertFalse($result[2]['is_settled']);
    }
}
