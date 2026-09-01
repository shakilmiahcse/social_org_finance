<?php

namespace Tests\Feature;

use App\Models\CampaignAdjustment;
use App\Models\Donor;
use App\Models\Fund;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinanceTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = Organization::factory()->create(['name' => 'Test Welfare Org']);
        $this->adminUser = User::factory()->create([
            'organization_id' => $this->org->id,
            'name' => 'Admin User',
            'email' => 'admin@testorg.com'
        ]);

        $permissions = [
            'funds.view', 'funds.create', 'funds.edit', 'funds.delete',
            'donors.view', 'donors.create', 'donors.edit', 'donors.delete',
            'transactions.view', 'transactions.create', 'transactions.edit', 'transactions.delete',
            'adjustments.view', 'adjustments.create', 'adjustments.delete',
            'reports.view', 'reports.export'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate([
            'name' => 'admin',
            'organization_id' => $this->org->id,
            'guard_name' => 'web'
        ]);
        $role->syncPermissions(Permission::all());
        $this->adminUser->assignRole($role);
    }

    public function test_fund_balance_calculates_correctly_with_credits_and_debits(): void
    {
        $fund = Fund::factory()->create([
            'organization_id' => $this->org->id,
            'created_by' => $this->adminUser->id,
            'type' => 'main'
        ]);

        // Add 5000 credit
        Transaction::factory()->create([
            'organization_id' => $this->org->id,
            'fund_id' => $fund->id,
            'amount' => 5000.00,
            'type' => 'credit',
            'status' => 'completed',
        ]);

        // Add 2000 credit
        Transaction::factory()->create([
            'organization_id' => $this->org->id,
            'fund_id' => $fund->id,
            'amount' => 2000.00,
            'type' => 'credit',
            'status' => 'completed',
        ]);

        // Add 3000 debit (expense)
        Transaction::factory()->create([
            'organization_id' => $this->org->id,
            'fund_id' => $fund->id,
            'amount' => 3000.00,
            'type' => 'debit',
            'status' => 'completed',
        ]);

        // 5000 + 2000 - 3000 = 4000
        $this->assertEquals(4000.00, $fund->getBalance());
    }

    public function test_campaign_adjustment_to_campaign_transfers_money_correctly(): void
    {
        $mainFund = Fund::factory()->create([
            'organization_id' => $this->org->id,
            'created_by' => $this->adminUser->id,
            'type' => 'main',
            'name' => 'Main Fund'
        ]);

        $campaignFund = Fund::factory()->create([
            'organization_id' => $this->org->id,
            'created_by' => $this->adminUser->id,
            'type' => 'campaign',
            'name' => 'Relief Fund'
        ]);

        // Initial deposit in main fund: 10,000
        Transaction::factory()->create([
            'organization_id' => $this->org->id,
            'fund_id' => $mainFund->id,
            'amount' => 10000.00,
            'type' => 'credit',
            'status' => 'completed',
        ]);

        $this->actingAs($this->adminUser)
            ->withSession(['organization_id' => $this->org->id])
            ->post(route('adjustments.store'), [
                'main_fund_id' => $mainFund->id,
                'campaign_fund_id' => $campaignFund->id,
                'amount' => 4000.00,
                'type' => 'to_campaign',
                'note' => 'Allocating money for relief'
            ]);

        // Main fund should have 10,000 - 4,000 = 6,000
        $this->assertEquals(6000.00, $mainFund->getBalance());

        // Campaign fund should have 4,000
        $this->assertEquals(4000.00, $campaignFund->getBalance());
    }

    public function test_campaign_adjustment_to_main_returns_unspent_funds_correctly(): void
    {
        $mainFund = Fund::factory()->create([
            'organization_id' => $this->org->id,
            'created_by' => $this->adminUser->id,
            'type' => 'main',
            'name' => 'Main Fund'
        ]);

        $campaignFund = Fund::factory()->create([
            'organization_id' => $this->org->id,
            'created_by' => $this->adminUser->id,
            'type' => 'campaign',
            'name' => 'Relief Fund'
        ]);

        // Initial 5,000 in campaign fund
        Transaction::factory()->create([
            'organization_id' => $this->org->id,
            'fund_id' => $campaignFund->id,
            'amount' => 5000.00,
            'type' => 'credit',
            'status' => 'completed',
        ]);

        // Return 2,000 from Campaign back to Main fund
        $this->actingAs($this->adminUser)
            ->withSession(['organization_id' => $this->org->id])
            ->post(route('adjustments.store'), [
                'main_fund_id' => $mainFund->id,
                'campaign_fund_id' => $campaignFund->id,
                'amount' => 2000.00,
                'type' => 'to_main',
                'note' => 'Returning unspent relief funds'
            ]);

        // Campaign fund balance should be 5,000 - 2,000 = 3,000
        $this->assertEquals(3000.00, $campaignFund->getBalance());

        // Main fund balance should be +2,000
        $this->assertEquals(2000.00, $mainFund->getBalance());
    }

    public function test_user_cannot_update_or_delete_other_organization_donor_idor_prevention(): void
    {
        $otherOrg = Organization::factory()->create(['name' => 'Other Org']);
        $otherDonor = Donor::factory()->create([
            'organization_id' => $otherOrg->id,
            'name' => 'Confidential Donor'
        ]);

        // Try to update other org's donor
        $response = $this->actingAs($this->adminUser)
            ->withSession(['organization_id' => $this->org->id])
            ->put(route('donors.update', $otherDonor->id), [
                'name' => 'Hacked Name',
                'phone' => '01700000000',
            ]);

        $response->assertStatus(403);
        $this->assertEquals('Confidential Donor', $otherDonor->fresh()->name);

        // Try to delete other org's donor
        $deleteResponse = $this->actingAs($this->adminUser)
            ->withSession(['organization_id' => $this->org->id])
            ->delete(route('donors.destroy', $otherDonor->id));

        $deleteResponse->assertStatus(403);
        $this->assertDatabaseHas('donors', ['id' => $otherDonor->id]);
    }

    public function test_user_cannot_update_or_delete_other_organization_fund_idor_prevention(): void
    {
        $otherOrg = Organization::factory()->create(['name' => 'Other Org']);
        $otherFund = Fund::factory()->create([
            'organization_id' => $otherOrg->id,
            'name' => 'Other Org Fund',
            'type' => 'main'
        ]);

        // Try to update other org's fund
        $response = $this->actingAs($this->adminUser)
            ->withSession(['organization_id' => $this->org->id])
            ->put(route('funds.update', $otherFund->id), [
                'name' => 'Hacked Fund Name',
                'type' => 'main',
            ]);

        $response->assertStatus(403);
        $this->assertEquals('Other Org Fund', $otherFund->fresh()->name);

        // Try to delete other org's fund
        $deleteResponse = $this->actingAs($this->adminUser)
            ->withSession(['organization_id' => $this->org->id])
            ->delete(route('funds.destroy', $otherFund->id));

        $deleteResponse->assertStatus(403);
        $this->assertDatabaseHas('funds', ['id' => $otherFund->id]);
    }

    public function test_reports_export_streams_csv_successfully(): void
    {
        $fund = Fund::factory()->create([
            'organization_id' => $this->org->id,
            'type' => 'main',
            'created_by' => $this->adminUser->id
        ]);

        Transaction::factory()->create([
            'organization_id' => $this->org->id,
            'fund_id' => $fund->id,
            'amount' => 15000.00,
            'type' => 'credit',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['organization_id' => $this->org->id])
            ->get(route('reports.export', [
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'report_type' => 'summary'
            ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
