<?php

namespace App\Http\Controllers;

use App\Models\CampaignAdjustment;
use App\Models\Transaction;
use App\Models\Fund;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CampaignAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('adjustments.view')) {
            abort(403, 'You do not have permission to view campaign adjustments.');
        }
        $organization_id = request()->session()->get("organization_id");

        $adjustments = CampaignAdjustment::with(['createdBy', 'updatedBy'])
            ->leftJoin('funds as main', 'campaign_adjustments.main_fund_id', '=', 'main.id')
            ->leftJoin('funds as campaign', 'campaign_adjustments.campaign_fund_id', '=', 'campaign.id')
            ->where('campaign_adjustments.organization_id', $organization_id)
            ->select(
                'campaign_adjustments.*',
                'campaign.name as campaign_fund_name',
                'main.name as main_fund_name',
            )
            ->orderBy('campaign_adjustments.created_at', 'desc')
            ->get()
            ->map(function ($adjustment) {
                return [
                    'id' => $adjustment->id,
                    'adjustment_amount' => number_format($adjustment->amount, 2),
                    'note' => $adjustment->note,
                    'campaign_fund_name' => $adjustment->campaign_fund_name,
                    'main_name' => $adjustment->main_fund_name,
                    'type' => $adjustment->type,
                    'createdBy' => $adjustment->createdBy ? ['name' => $adjustment->createdBy->name] : null,
                    'updatedBy' => $adjustment->updatedBy ? ['name' => $adjustment->updatedBy->name] : null,
                    'created_at' => Carbon::parse($adjustment->created_at)->format('j F, Y g:i A'),
                    'updated_at' => Carbon::parse($adjustment->updated_at)->format('j F, Y g:i A'),
                ];
            });

        $funds = Fund::get();

        return Inertia::render('CampaignAdjustments/Index', [
            'adjustments' => $adjustments,
            'funds' => $funds,
            'permissions' => [
                'view' => auth()->user()->can('adjustments.view'),
                'edit' => auth()->user()->can('adjustments.edit'),
                'delete' => auth()->user()->can('adjustments.delete'),
                'create' => auth()->user()->can('adjustments.create'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('adjustments.create')) {
            abort(403, 'You do not have permission to create campaign adjustments.');
        }
        $organization_id = request()->session()->get("organization_id");

        $mainFunds = Fund::getMainDropdown();
        $campaignFunds = Fund::leftJoin('transactions as t', 'funds.id', '=', 't.fund_id')
        ->whereNull('funds.closed_at')
        ->where('funds.type', 'campaign')
        ->where('funds.organization_id', $organization_id)
        ->select(
            'funds.id',
            'funds.name',
            DB::raw("
                SUM(CASE WHEN t.type = 'credit' AND t.status = 'completed' THEN t.amount ELSE 0 END) -
                SUM(CASE WHEN t.type = 'debit' AND t.status = 'completed' THEN t.amount ELSE 0 END) AS total_amount
            ")
        )
        ->groupBy('funds.id', 'funds.name')
        ->get()
        ->map(function ($fund) {
            return [
                'id' => $fund->id,
                'name' => $fund->name,
                'amount' => $fund->total_amount,
            ];
        })
        ->values();

        return Inertia::render('CampaignAdjustments/Create', [
            'mainFunds' => $mainFunds,
            'campaignFunds' => $campaignFunds
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('adjustments.create')) {
            abort(403, 'You do not have permission to create campaign adjustments.');
        }
        $validated = $request->validate([
            'amount' => 'required|numeric|not_in:0',
            'type' => 'required|in:to_campaign,to_main',
            'main_fund_id' => 'required|exists:funds,id',
            'campaign_fund_id' => 'required|exists:funds,id',
            'note' => 'nullable|string',
        ]);

        $organization_id = $request->session()->get("organization_id");

        try {
            DB::beginTransaction();

            $amount = abs($validated['amount']);

            // Create adjustment record
            $adjustment = CampaignAdjustment::create([
                'organization_id' => $organization_id,
                'amount' => $amount,
                'type' => $validated['type'],
                'main_fund_id' => $validated['main_fund_id'],
                'campaign_fund_id' => $validated['campaign_fund_id'],
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Fetch related fund names using relationships
            $mainFund = $adjustment->mainFund;
            $campaignFund = $adjustment->campaignFund;

            // Generate unique transaction IDs based on current timestamp
            $lastTransaction = Transaction::latest('id')->first();
            $nextId = $lastTransaction ? $lastTransaction->id + 1 : 1;
            $txn_id1 = 'TXN' . time() . $nextId;
            $txn_id2 = 'TXN' . time() . ($nextId + 1);

            if ($adjustment->type === 'to_campaign') {
                // Debit main fund
                Transaction::create([
                    'organization_id' => $organization_id,
                    'txn_id' => $txn_id1,
                    'fund_id' => $mainFund->id,
                    'adjustment_id' => $adjustment->id,
                    'amount' => $amount,
                    'payment_method' => 'adjustment',
                    'status' => 'completed',
                    'type' => 'debit',
                    'purpose' => 'Campaign Adjustment',
                    'note' => 'Adjustment to Campaign Fund: ' . $campaignFund->name,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                // Credit campaign fund
                Transaction::create([
                    'organization_id' => $organization_id,
                    'txn_id' => $txn_id2,
                    'fund_id' => $campaignFund->id,
                    'adjustment_id' => $adjustment->id,
                    'amount' => $amount,
                    'payment_method' => 'adjustment',
                    'status' => 'completed',
                    'type' => 'credit',
                    'purpose' => 'Campaign Adjustment',
                    'note' => 'Received from Main Fund: ' . $mainFund->name,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            if ($adjustment->type === 'to_main') {
                // Debit campaign fund
                Transaction::create([
                    'organization_id' => $organization_id,
                    'txn_id' => $txn_id1,
                    'fund_id' => $campaignFund->id,
                    'adjustment_id' => $adjustment->id,
                    'amount' => $amount,
                    'payment_method' => 'adjustment',
                    'status' => 'completed',
                    'type' => 'credit',
                    'purpose' => 'Campaign Return',
                    'note' => 'Returned to Main Fund: ' . $mainFund->name,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                // Credit main fund
                Transaction::create([
                    'organization_id' => $organization_id,
                    'txn_id' => $txn_id2,
                    'fund_id' => $mainFund->id,
                    'adjustment_id' => $adjustment->id,
                    'amount' => $amount,
                    'payment_method' => 'adjustment',
                    'status' => 'completed',
                    'type' => 'debit',
                    'purpose' => 'Campaign Return',
                    'note' => 'Received from Campaign Fund: ' . $campaignFund->name,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('adjustments.index')->with('success', 'Adjustment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'Something went wrong. Please try again.']);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('adjustments.delete')) {
            abort(403, 'You do not have permission to delete campaign adjustments.');
        }
        try {
            DB::beginTransaction();

            $adjustment = CampaignAdjustment::findOrFail($id);

            // Only delete transactions related to this specific adjustment
            Transaction::where('adjustment_id', $adjustment->id)->delete();

            // Delete the adjustment itself
            $adjustment->delete();

            DB::commit();
            return redirect()->route('adjustments.index')->with('success', 'Adjustment and related transactions deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'Failed to delete adjustment. Please try again.']);
        }
    }

}
