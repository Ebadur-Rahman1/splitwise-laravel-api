<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    // List expenses of a group
    public function index($groupId)
    {
        $group = Group::with('expenses.splits')->findOrFail($groupId);

        // Optional: check membership
        if (!$group->users()->where('user_id', auth('api')->id())->exists()) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        return response()->json($group->expenses);
    }

    // Add expense and auto split
    public function store(Request $request, $groupId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        $group = Group::findOrFail($groupId);

        // Check membership
        if (!$group->users()->where('user_id', auth('api')->id())->exists()) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        DB::transaction(function () use ($request, $group) {

            $expense = Expense::create([
                'group_id' => $group->id,
                'paid_by' => auth('api')->id(),
                'title' => $request->title,
                'amount' => $request->amount,
            ]);

            $members = $group->users;

            $splitAmount = round($request->amount / count($members), 2);

            foreach ($members as $member) {
                ExpenseSplit::create([
                    'expense_id' => $expense->id,
                    'user_id' => $member->id,
                    'amount' => $splitAmount,
                    'is_settled' => false
                ]);
            }
        });

        return response()->json([
            'message' => 'Expense added and split successfully'
        ]);
    }
}
