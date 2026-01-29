<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // List my groups
    public function index()
    {
        return response()->json(auth('api')->user()->groups);
    }

    // Create group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $group = Group::create([
            'name' => $request->name,
            'created_by' => auth('api')->id()
        ]);

        // Add creator as member
        $group->users()->attach(auth('api')->id());

        return response()->json($group);
    }

    // Join group
    public function join($id)
    {
        $group = Group::findOrFail($id);

        $group->users()->syncWithoutDetaching(auth('api')->id());

        return response()->json([
            'message' => 'Joined group successfully'
        ]);
    }

    // Leave group
    public function leave($id)
    {
        $group = Group::findOrFail($id);

        if (! $group->users()->where('user_id', auth('api')->id())->exists()) {
            return response()->json(['error' => 'You are not in this group'], 403);
        }

        $due = ExpenseSplit::where('user_id', auth('api')->id())
            ->where('is_settled', false)
            ->whereHas('expense', function ($q) use ($id) {
                $q->where('group_id', $id);
            })
            ->sum('amount');

        if ($due > 0) {
            return response()->json([
                'error' => 'You have pending dues. Clear them first.'
            ], 403);
        }

        $group->users()->detach(auth('api')->id());

        return response()->json([
            'message' => 'Left group successfully'
        ]);
    }

    // Get group balances
    public function groupBalances($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (! $group->users()->where('users.id', auth('api')->id())->exists()) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        $totalExpense = Expense::where('group_id', $groupId)->sum('amount');
        $memberCount = $group->users->count();

        $fairShare = round($totalExpense / $memberCount, 2);

        $paidMap = Expense::where('group_id', $groupId)
            ->selectRaw('paid_by, SUM(amount) as total_paid')
            ->groupBy('paid_by')
            ->pluck('total_paid', 'paid_by')
            ->toArray();

        $result = [];

        foreach ($group->users as $user) {
            $paid = round($paidMap[$user->id] ?? 0, 2);
            $balance = round($paid - $fairShare, 2);

            $result[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'paid' => $paid,
                'fair_share' => $fairShare,
                'balance' => $balance,
            ];
        }

        return response()->json([
            'group_id' => $groupId,
            'total_expense' => $totalExpense,
            'fair_share_per_person' => $fairShare,
            'members' => $result
        ]);
    }

    // Settlement suggestion
    public function groupSettlement($groupId)
    {
        $balanceResponse = $this->groupBalances($groupId)->getData(true);

        $members = $balanceResponse['members'];

        $creditors = [];
        $debtors = [];

        foreach ($members as $m) {
            if ($m['balance'] > 0) {
                $creditors[] = [
                    'name' => $m['name'],
                    'amount' => $m['balance']
                ];
            } elseif ($m['balance'] < 0) {
                $debtors[] = [
                    'name' => $m['name'],
                    'amount' => abs($m['balance'])
                ];
            }
        }

        $settlements = [];
        $i = 0;
        $j = 0;

        while ($i < count($debtors) && $j < count($creditors)) {
            $pay = min($debtors[$i]['amount'], $creditors[$j]['amount']);

            $settlements[] = [
                'from' => $debtors[$i]['name'],
                'to' => $creditors[$j]['name'],
                'amount' => round($pay, 2)
            ];

            $debtors[$i]['amount'] -= $pay;
            $creditors[$j]['amount'] -= $pay;

            if ($debtors[$i]['amount'] == 0) $i++;
            if ($creditors[$j]['amount'] == 0) $j++;
        }

        return response()->json([
            'group_id' => $groupId,
            'settlements' => $settlements
        ]);
    }
}
