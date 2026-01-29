<?php

namespace App\Services;

class SettlementService
{
    /**
     * @param array $splits
     * Each split:
     * [
     *   'id' => int,
     *   'user_id' => int,
     *   'amount' => float,
     *   'is_settled' => bool
     * ]
     */
    public function settleUserSplits(int $userId, array $splits): array
    {
        foreach ($splits as &$split) {
            if ($split['user_id'] === $userId && $split['is_settled'] === false) {
                $split['is_settled'] = true;
            }
        }

        return $splits;
    }
}
