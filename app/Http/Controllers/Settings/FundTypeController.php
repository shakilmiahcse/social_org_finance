<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Fund;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FundTypeController extends Controller
{
    public function edit()
    {
        if (!auth()->user()->can('settings.view')) {
            abort(403, 'You do not have permission to edit fund types.');
        }
        $organization_id = request()->session()->get("organization_id");
        // Get the main fund
        $main_fund = Fund::where('type', 'main')->where('organization_id', $organization_id)->first();

        // Check if a main fund is found and get its id
        $main_fund_id = $main_fund ? $main_fund->id : null;

        // Pass funds and main_fund_id to the Inertia render
        return Inertia::render('settings/FundType', [
            'funds' => Fund::whereNull('closed_at')->where('organization_id', $organization_id)->get(['id', 'name', 'type']),
            'main_fund_id' => $main_fund_id,  // Pass the main fund ID to the component
            'can' => [
                'view' => auth()->user()->can('settings.view'),
                'update' => auth()->user()->can('settings.update'),
            ],
        ]);
    }


    public function update(Request $request)
    {
        if (!auth()->user()->can('settings.update')) {
            abort(403, 'You do not have permission to update fund types.');
        }
        $organization_id = request()->session()->get("organization_id");

        $request->validate([
            'main_fund_id' => 'required|exists:funds,id',
        ]);

        // Set all funds to 'campaign' first
        Fund::where('type', 'main')->where('organization_id', $organization_id)->update(['type' => 'campaign']);

        // Then set the selected fund as 'main'
        Fund::where('id', $request->main_fund_id)->where('organization_id', $organization_id)->update(['type' => 'main']);

        return back()->with('status', 'Fund type updated successfully.');
    }
}
