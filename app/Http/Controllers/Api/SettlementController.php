<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseSplit;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
{
    /**
     * Settle all dues of current user in a group
     */
    public function settle(Request $request, $groupId)
    {
        $userId = auth('api')->id();

        $group = Group::findOrFail($groupId);

        // Check membership
        if (!$group->users()->where('user_id', $userId)->exists()) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        // Get all unsettled splits of this user in this group
        $splits = ExpenseSplit::where('user_id', $userId)
            ->where('is_settled', false)
            ->whereHas('expense', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })
            ->get();

        if ($splits->isEmpty()) {
            return response()->json(['message' => 'No dues to settle']);
        }

        DB::transaction(function () use ($splits) {
            foreach ($splits as $split) {
                $split->update(['is_settled' => true]);
            }
        });

        return response()->json([
            'message' => 'All dues settled successfully'
        ]);
    }
}
