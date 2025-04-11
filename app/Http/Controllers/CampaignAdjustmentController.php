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
        $adjustments = CampaignAdjustment::with(['fund', 'createdBy', 'updatedBy'])
            ->leftJoin('funds as main', 'campaign_adjustments.main_fund_id', '=', 'main.id')
            ->leftJoin('funds as campaign', 'campaign_adjustments.campaign_fund_id', '=', 'campaign.id')
            ->select(
                'campaign_adjustments.*',
                'campaign.name as campaign_fund_name',
                'campaign.type as campaign_fund_type',
                'main.name as main_fund_name',
                'main.type as main_fund_type',
            )
            ->orderBy('campaign_adjustments.created_at', 'desc')
            ->get()
            ->map(function ($adjustment) {
                return [
                    'id' => $adjustment->id,
                    'adjustment_amount' => number_format($adjustment->amount, 2),
                    'note' => $adjustment->note,
                    'fund_name' => $adjustment->campaign_fund_name,
                    'fund_type' => $adjustment->campaign_fund_type,
                    'main_name' => $adjustment->main_fund_name,
                    'main_type' => $adjustment->main_fund_type,
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
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mainFunds = Fund::getMainDropdown();
        $campaignFunds = Fund::getCampaignDropdown();

        return Inertia::render('CampaignAdjustments/create', [
            'mainFunds' => $mainFunds,
            'campaignFunds' => $campaignFunds
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:to_campaign,to_main',
            'main_fund_id' => 'required|exists:funds,id',
            'campaign_fund_id' => 'required|exists:funds,id',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create adjustment record
            $adjustment = CampaignAdjustment::create([
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'main_fund_id' => $validated['main_fund_id'],
                'campaign_fund_id' => $validated['campaign_fund_id'],
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Fetch related fund names using relationships
            $mainFund = $adjustment->mainFund;          // returns Fund model
            $campaignFund = $adjustment->campaignFund;  // returns Fund model

            // Get next transaction IDs
            $lastTransaction = Transaction::latest('id')->first();
            $nextId = $lastTransaction ? $lastTransaction->id + 1 : 1;
            $txn_id1 = 'TXN' . $nextId;
            $txn_id2 = 'TXN' . ($nextId + 1);

            if ($adjustment->type === 'to_campaign') {
                // Debit main fund
                Transaction::create([
                    'txn_id' => $txn_id1,
                    'fund_id' => $mainFund->id,
                    'amount' => $adjustment->amount,
                    'type' => 'debit',
                    'purpose' => 'Campaign Adjustment',
                    'note' => 'Adjustment to Campaign Fund: ' . $campaignFund->name,
                    'created_by' => auth()->id(),
                ]);

                // Credit campaign fund
                Transaction::create([
                    'txn_id' => $txn_id2,
                    'fund_id' => $campaignFund->id,
                    'amount' => $adjustment->amount,
                    'type' => 'credit',
                    'purpose' => 'Campaign Adjustment',
                    'note' => 'Received from Main Fund: ' . $mainFund->name,
                    'created_by' => auth()->id(),
                ]);
            }

            if ($adjustment->type === 'to_main') {
                // Debit campaign fund
                Transaction::create([
                    'txn_id' => $txn_id1,
                    'fund_id' => $campaignFund->id,
                    'amount' => $adjustment->amount,
                    'type' => 'debit',
                    'purpose' => 'Campaign Return',
                    'note' => 'Returned to Main Fund: ' . $mainFund->name,
                    'created_by' => auth()->id(),
                ]);

                // Credit main fund
                Transaction::create([
                    'txn_id' => $txn_id2,
                    'fund_id' => $mainFund->id,
                    'amount' => $adjustment->amount,
                    'type' => 'credit',
                    'purpose' => 'Campaign Return',
                    'note' => 'Received from Campaign Fund: ' . $campaignFund->name,
                    'created_by' => auth()->id(),
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
    public function destroy(string $id)
    {
        //
    }
}
