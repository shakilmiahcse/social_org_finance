<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organization_id = request()->session()->get("organization_id");

        $funds = Fund::with(['createdBy', 'updatedBy'])
            ->leftJoin('transactions as t', 'funds.id', '=', 't.fund_id')
            ->where('funds.organization_id', $organization_id)
            ->select(
                'funds.id',
                'funds.name',
                'funds.description',
                'funds.type',
                'funds.created_by',
                'funds.updated_by',
                'funds.created_at',
                'funds.updated_at',
                DB::raw("SUM(CASE WHEN t.type = 'credit' AND t.status = 'completed' THEN t.amount ELSE 0 END) -
                        SUM(CASE WHEN t.type = 'debit' AND t.status = 'completed' THEN t.amount ELSE 0 END) as total_sum_amount")
            )
            ->groupBy([
                'funds.id',
                'funds.name',
                'funds.description',
                'funds.type',
                'funds.created_by',
                'funds.updated_by',
                'funds.created_at',
                'funds.updated_at'
            ])
            ->orderBy('funds.created_at', 'desc')
            ->get()
            ->map(function ($fund) {
                return [
                    'id' => $fund->id,
                    'name' => $fund->name,
                    'description' => $fund->description,
                    'type' => $fund->type,
                    'total_amount' => number_format($fund->total_sum_amount, 2),
                    'createdBy' => $fund->createdBy ? ['name' => $fund->createdBy->name] : null,
                    'updatedBy' => $fund->updatedBy ? ['name' => $fund->updatedBy->name] : null,
                    'created_at' => Carbon::parse($fund->created_at)->format('j F, Y g:i A'),
                    'updated_at' => Carbon::parse($fund->updated_at)->format('j F, Y g:i A'),
                ];
            });

        return Inertia::render('Funds/Index', [
            'funds' => $funds
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:main,campaign',
        ]);

        $organization_id = $request->session()->get("organization_id");

        try {
            $fund = Fund::create([
                'organization_id' => $organization_id,
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('funds.index')
                ->with('success', 'Fund created successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create fund. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Fund $fund)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fund $fund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fund $fund)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:main,campaign',
            'total_amount' => 'required|numeric|min:0',
        ]);

        try {
            $fund->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'total_amount' => $validated['total_amount'],
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('funds.index')
                ->with('success', 'Fund updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update fund. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Fund $fund)
    {
        $fund->delete();
        return redirect()->route('funds.index')->with('success', 'Funds deleted successfully.');
    }
}
