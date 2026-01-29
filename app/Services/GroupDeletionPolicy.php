<?php

namespace App\Services;

class GroupDeletionPolicy
{
    /**
     * @param array $splits
     * Each split:
     * [
     *   'is_settled' => bool
     * ]
     */
    public function canDeleteGroup(array $splits): bool
    {
        foreach ($splits as $split) {
            if ($split['is_settled'] === false) {
                return false; // Found an unsettled due
            }
        }

        return true; // All settled
    }
}
