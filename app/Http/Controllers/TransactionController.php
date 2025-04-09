<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Donor;
use App\Models\Fund;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['createdBy', 'updatedBy'])
            ->leftJoin('donors', 'transactions.donor_id', '=', 'donors.id')
            ->leftJoin('funds', 'transactions.fund_id', '=', 'funds.id')
            ->select(
                'transactions.*',
                'donors.name as donor_name',
                'funds.name as fund_name',
            )
            ->orderBy('transactions.created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'txn_id' => $transaction->txn_id,
                    'status' => $transaction->status,
                    'donor' => $transaction->donor_name ? ['name' => $transaction->donor_name] : null,
                    'fund' => $transaction->fund_name ? ['name' => $transaction->fund_name] : null,
                    'amount' => number_format($transaction->amount, 2),
                    'type' => $transaction->type,
                    'purpose' => $transaction->purpose,
                    'payment_method' => $transaction->payment_method,
                    'reference' => $transaction->reference,
                    'note' => $transaction->note,
                    'createdBy'   => $transaction->createdBy ? ['name' => $transaction->createdBy->name] : null,
                    'updatedBy'   => $transaction->updatedBy ? ['name' => $transaction->updatedBy->name] : null,
                    'created_at' => Carbon::parse($transaction->created_at)->format('j F, Y g:i A'),
                    'updated_at' => Carbon::parse($transaction->updated_at)->format('j F, Y g:i A'),
                ];
            });

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $donors = Donor::getDropdown();  // Get donor dropdown data
        $funds = Fund::getDropdown();    // Get fund dropdown data

        return Inertia::render('Transactions/create', [
            'donors' => $donors,
            'funds' => $funds
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'txn_id' => 'required|string|unique:transactions,txn_id|max:255',
            'donor_id' => 'required|exists:donors,id',
            'fund_id' => 'required|exists:funds,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:credit,debit',
            'purpose' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,bkash,card,bank',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,completed,canceled',
        ]);

        // Create a new transaction record
        $transaction = Transaction::create([
            'txn_id' => $validated['txn_id'],
            'donor_id' => $validated['donor_id'],
            'fund_id' => $validated['fund_id'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'purpose' => $validated['purpose'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'],
            'note' => $validated['note'],
            'status' => $validated['status'] ?? 'pending',
        ]);

        // Redirect back to transactions page with a success message
        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
