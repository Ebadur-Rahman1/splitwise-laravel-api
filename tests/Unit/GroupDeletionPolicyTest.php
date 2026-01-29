<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\GroupDeletionPolicy;

class GroupDeletionPolicyTest extends TestCase
{
    public function test_group_cannot_be_deleted_if_any_unsettled_split_exists()
    {
        $policy = new GroupDeletionPolicy();

        $splits = [
            ['is_settled' => true],
            ['is_settled' => false], // one unpaid
            ['is_settled' => true],
        ];

        $result = $policy->canDeleteGroup($splits);

        $this->assertFalse($result);
    }

    public function test_group_can_be_deleted_if_all_splits_are_settled()
    {
        $policy = new GroupDeletionPolicy();

        $splits = [
            ['is_settled' => true],
            ['is_settled' => true],
            ['is_settled' => true],
        ];

        $result = $policy->canDeleteGroup($splits);

        $this->assertTrue($result);
    }

    public function test_group_can_be_deleted_if_there_are_no_splits()
    {
        $policy = new GroupDeletionPolicy();

        $splits = []; // No expenses at all

        $result = $policy->canDeleteGroup($splits);

        $this->assertTrue($result);
    }
}
