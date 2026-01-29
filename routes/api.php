<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ExpenseController;
use App\Models\ExpenseSplit;
use App\Http\Controllers\Api\SettlementController;

Route::get('/status', function () {
    return response()->json([
        'app' => 'Splitwise API',
        'status' => 'running'
    ]);
});

// =======================
// Public routes
// =======================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// =======================
// Protected routes
// =======================
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // =======================
    // Group APIs
    // =======================
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups/{id}/join', [GroupController::class, 'join']);
    Route::post('/groups/{id}/leave', [GroupController::class, 'leave']);

    // =======================
    // Expense APIs
    // =======================
    Route::post('/groups/{groupId}/expenses', [ExpenseController::class, 'store']);
    Route::get('/groups/{groupId}/expenses', [ExpenseController::class, 'index']);

    // =======================
    // Balance API (STEP 5.4)
    // =======================
    Route::get('/balance', function () {

        $userId = auth()->id();

        // How much I owe to others
        $youOwe = ExpenseSplit::where('user_id', $userId)
            ->where('is_settled', false)
            ->sum('amount');

        // How much others owe me
        $owedToYou = ExpenseSplit::whereHas('expense', function ($q) use ($userId) {
            $q->where('paid_by', $userId);
        })
            ->where('user_id', '!=', $userId)
            ->where('is_settled', false)
            ->sum('amount');

        return response()->json([
            'you_owe' => $youOwe,
            'owed_to_you' => $owedToYou,
        ]);
    });

    Route::post('/groups/{groupId}/settle', [SettlementController::class, 'settle']);
    Route::delete('/groups/{groupId}', [GroupController::class, 'destroy']);

    // get group balances
    Route::get('/groups/{groupId}/balances', [GroupController::class, 'groupBalances']);

    // get group settlement suggestion
    Route::get('/groups/{groupId}/settlement-suggestion', [GroupController::class, 'groupSettlement']);



});
