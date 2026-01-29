<?php

namespace App\Services;

class DebtAggregator
{
    /**
     * @param array $splits
     * Each split:
     * [
     *   'paid_by' => int,
     *   'user_id' => int,
     *   'amount' => float,
     *   'is_settled' => bool
     * ]
     *
     * @return array
     * [
     *   debtor => [
     *       creditor => amount
     *   ]
     * ]
     */
    public function aggregate(array $splits): array
    {
        $debts = [];

        foreach ($splits as $split) {

            if ($split['is_settled']) {
                continue;
            }

            $debtor = $split['user_id'];
            $creditor = $split['paid_by'];

            // If same person paid for himself, ignore
            if ($debtor === $creditor) {
                continue;
            }

            if (!isset($debts[$debtor])) {
                $debts[$debtor] = [];
            }

            if (!isset($debts[$debtor][$creditor])) {
                $debts[$debtor][$creditor] = 0;
            }

            $debts[$debtor][$creditor] += $split['amount'];
        }

        return $debts;
    }
}
