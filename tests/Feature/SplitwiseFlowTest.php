<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class SplitwiseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_splitwise_flow()
    {
        // ==============================
        // 1. Create Users
        // ==============================
        $ebaad = User::factory()->create(['name' => 'Ebaad']);
        $saif = User::factory()->create(['name' => 'Saif']);
        $zeeshan = User::factory()->create(['name' => 'Zeeshan']);

        // ==============================
        // 2. Ebaad creates group
        // ==============================
        $this->actingAs($ebaad, 'api');

        $res = $this->postJson('/api/groups', [
            'name' => 'aws'
        ]);

        $res->assertStatus(200);

        $groupId = $res->json('id');

        // ==============================
        // 3. Saif & Zeeshan join group
        // ==============================
        $this->actingAs($saif, 'api');
        $this->postJson("/api/groups/{$groupId}/join")->assertStatus(200);

        $this->actingAs($zeeshan, 'api');
        $this->postJson("/api/groups/{$groupId}/join")->assertStatus(200);

        // Safety check
        $this->assertDatabaseCount('group_user', 3);

        // ==============================
        // 4. Add Expenses
        // ==============================

        $this->actingAs($saif, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Lunch',
            'amount' => 1000
        ])->assertStatus(200);

        $this->actingAs($ebaad, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Movie',
            'amount' => 600
        ])->assertStatus(200);

        $this->actingAs($zeeshan, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Snacks',
            'amount' => 300
        ])->assertStatus(200);

        $this->actingAs($saif, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Dinner',
            'amount' => 900
        ])->assertStatus(200);

        $this->actingAs($ebaad, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Travel',
            'amount' => 1200
        ])->assertStatus(200);

        $this->actingAs($zeeshan, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Coffee',
            'amount' => 450
        ])->assertStatus(200);

        $this->actingAs($saif, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Breakfast',
            'amount' => 300
        ])->assertStatus(200);

        $this->actingAs($ebaad, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Shopping',
            'amount' => 1500
        ])->assertStatus(200);

        $this->actingAs($zeeshan, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Ice Cream',
            'amount' => 150
        ])->assertStatus(200);

        $this->actingAs($saif, 'api');
        $this->postJson("/api/groups/{$groupId}/expenses", [
            'title' => 'Fast Food',
            'amount' => 600
        ])->assertStatus(200);

        // ==============================
        // 5. Check Group Balances
        // ==============================

        $this->actingAs($ebaad, 'api');
        $balanceRes = $this->getJson("/api/groups/{$groupId}/balances");
        $balanceRes->assertStatus(200);

        $members = collect($balanceRes->json('members'));

        $ebaadBalance = round($members->firstWhere('name', 'Ebaad')['balance'], 2);
        $saifBalance = round($members->firstWhere('name', 'Saif')['balance'], 2);
        $zeeshanBalance = round($members->firstWhere('name', 'Zeeshan')['balance'], 2);

        $this->assertEquals(966.67, $ebaadBalance);
        $this->assertEquals(466.67, $saifBalance);
        $this->assertEquals(-1433.33, $zeeshanBalance);

        // ==============================
        // 6. Check Settlement Suggestion
        // ==============================

        $settleRes = $this->getJson("/api/groups/{$groupId}/settlement-suggestion");
        $settleRes->assertStatus(200);

        $settlements = collect($settleRes->json('settlements'));

        $this->assertTrue(
            $settlements->contains(fn($s) => $s['from'] === 'Zeeshan' && $s['to'] === 'Ebaad')
        );

        $this->assertTrue(
            $settlements->contains(fn($s) => $s['from'] === 'Zeeshan' && $s['to'] === 'Saif')
        );
    }
}
