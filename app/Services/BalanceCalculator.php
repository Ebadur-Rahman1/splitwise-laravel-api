<?php

namespace App\Services;

class BalanceCalculator
{
    public function calculate(int $userId, array $splits): array
    {
        $youOwe = 0;
        $owedToYou = 0;

        foreach ($splits as $split) {

            if ($split['is_settled']) {
                continue;
            }

            if ($split['user_id'] === $userId && $split['paid_by'] !== $userId) {
                $youOwe += $split['amount'];
            }

            if ($split['paid_by'] === $userId && $split['user_id'] !== $userId) {
                $owedToYou += $split['amount'];
            }
        }

        return [
            'you_owe' => $youOwe,
            'owed_to_you' => $owedToYou
        ];
    }
}
