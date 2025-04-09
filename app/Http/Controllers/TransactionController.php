<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        $transactions = Transaction::leftJoin('donors', 'transactions.donor_id', '=', 'donors.id')
            ->leftJoin('funds', 'transactions.fund_id', '=', 'funds.id')
            ->leftJoin('users as created_by', 'transactions.created_by', '=', 'created_by.id')
            ->leftJoin('users as updated_by', 'transactions.updated_by', '=', 'updated_by.id')
            ->select(
                'transactions.*',
                'donors.name as donor_name',
                'funds.name as fund_name',
                'created_by.name as created_by_name',
                'updated_by.name as updated_by_name'
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
                    'createdBy' => $transaction->created_by_name ? ['name' => $transaction->created_by_name] : null,
                    'updatedBy' => $transaction->updated_by_name ? ['name' => $transaction->updated_by_name] : null,
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
        return Inertia::render('Transactions/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
