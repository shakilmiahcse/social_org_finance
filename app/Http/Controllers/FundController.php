<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\Transaction;
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
        if (!auth()->user()->can('funds.view')) {
            abort(403, 'You do not have permission to view funds.');
        }
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
            'funds' => $funds,
            'permissions' => [
                'view' => auth()->user()->can('funds.view'),
                'edit' => auth()->user()->can('funds.edit'),
                'delete' => auth()->user()->can('funds.delete'),
                'create' => auth()->user()->can('funds.create'),
            ],
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
        if (!auth()->user()->can('funds.create')) {
            abort(403, 'You do not have permission to create funds.');
        }
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

    public function getDropdown()
    {
        if (!auth()->user()->can('funds.view')) {
            abort(403, 'You do not have permission to view funds.');
        }

        $organization_id = request()->session()->get("organization_id");

        try {
            $funds = Fund::leftJoin('transactions as t', 'funds.id', '=', 't.fund_id')
                ->where('funds.organization_id', $organization_id)
                ->select(
                    'funds.id',
                    'funds.name',
                    DB::raw("SUM(CASE WHEN t.type = 'credit' AND t.status = 'completed' THEN t.amount ELSE 0 END) -
                            SUM(CASE WHEN t.type = 'debit' AND t.status = 'completed' THEN t.amount ELSE 0 END) as total_sum_amount")
                )
                ->groupBy('funds.id', 'funds.name')
                ->orderBy('funds.created_at', 'desc')
                ->get()
                ->map(function ($fund) {
                    return [
                        'id' => $fund->id,
                        'name' => $fund->name . ' (' . number_format($fund->total_sum_amount, 2) . ')'
                    ];
                });

            return response()->json([
                'success' => true,
                'funds' => $funds
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
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
     * Show the history of the specified resource.
     */

    public function history(Fund $fund)
    {
        if (!auth()->user()->can('funds.view')) {
            abort(403, 'You do not have permission to view fund history.');
        }

        // Verify the fund belongs to the organization
        $organization_id = request()->session()->get("organization_id");
        if ($fund->organization_id !== $organization_id) {
            abort(403, 'You do not have permission to view this fund.');
        }

        // Get transactions with running balance
        $transactions = $fund->getTransactionsWithRunningBalance()
            ->map(function ($txn) {
                return [
                    'id' => $txn->id,
                    'txn_no' => $txn->txn_id,
                    'type' => $txn->type,
                    'amount' => $txn->amount,
                    'balance' => $txn->running_balance,
                    'purpose' => $txn->purpose,
                    'payment_method' => $txn->payment_method,
                    'reference' => $txn->reference,
                    'status' => $txn->status,
                    'created_at' => $txn->created_at,
                    'donor' => $txn->donor ? ['name' => $txn->donor->name] : null,
                    'createdBy' => $txn->createdBy ? ['name' => $txn->createdBy->name] : null,
                ];
            });

        // Calculate summary
        $summary = [
            'total_income' => $fund->transactions()
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_expense' => $fund->transactions()
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->sum('amount'),
            'current_balance' => $fund->getBalance(),
        ];

        // Get all funds for dropdown with total amount in brackets
        $funds = Fund::leftJoin('transactions as t', 'funds.id', '=', 't.fund_id')
            ->where('funds.organization_id', $organization_id)
            ->select(
                'funds.id',
                'funds.name',
                DB::raw("SUM(CASE WHEN t.type = 'credit' AND t.status = 'completed' THEN t.amount ELSE 0 END) -
                        SUM(CASE WHEN t.type = 'debit' AND t.status = 'completed' THEN t.amount ELSE 0 END) as total_sum_amount")
            )
            ->groupBy('funds.id', 'funds.name')
            ->orderBy('funds.name', 'asc')
            ->get()
            ->map(function ($fund) {
                return [
                    'id' => $fund->id,
                    'name' => $fund->name . ' (' . number_format($fund->total_sum_amount, 2) . ')'
                ];
            });


        return Inertia::render('Funds/History', [
            'funds' => $funds,
            'transactions' => $transactions,
            'summary' => $summary,
            'current_fund_id' => $fund->id,
        ]);
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
        if (!auth()->user()->can('funds.edit')) {
            abort(403, 'You do not have permission to edit funds.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:main,campaign',
        ]);

        try {
            $fund->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
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
        if (!auth()->user()->can('funds.delete')) {
            abort(403, 'You do not have permission to delete funds.');
        }
        $fund->delete();
        return redirect()->route('funds.index')->with('success', 'Funds deleted successfully.');
    }
}
