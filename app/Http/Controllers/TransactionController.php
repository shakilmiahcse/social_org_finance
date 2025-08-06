<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Donor;
use App\Models\Fund;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('transactions.view')) {
            abort(403, 'You do not have permission to view transactions.');
        }

        $organization_id = request()->session()->get("organization_id");

        $organization = Organization::where('id', $organization_id)->first();

        $query = Transaction::with(['createdBy', 'updatedBy', 'donor', 'fund'])
            ->where('transactions.organization_id', $organization_id);

        // Apply date range filter
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('transactions.created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Apply type filter
        if ($request->has('types')) {
            $query->whereIn('type', (array)$request->types);
        }

        // Apply status filter
        if ($request->has('statuses')) {
            $query->whereIn('status', (array)$request->statuses);
        }

        // Apply payment method filter (single value now)
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Apply created by filter (single value now)
        if ($request->has('created_by') && $request->created_by) {
            $query->whereHas('createdBy', function ($q) use ($request) {
                $q->where('name', $request->created_by);
            });
        }

        $transactions = $query->orderBy('transactions.created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'txn_id' => $transaction->txn_id,
                    'donor_id' => $transaction->donor_id,
                    'fund_id' => $transaction->fund_id,
                    'status' => $transaction->status,
                    'donor' => $transaction->donor ? ['name' => $transaction->donor->name] : null,
                    'fund' => $transaction->fund ? ['name' => $transaction->fund->name] : null,
                    'amount' => number_format($transaction->amount, 2),
                    'type' => $transaction->type,
                    'purpose' => $transaction->purpose,
                    'payment_method' => $transaction->payment_method,
                    'reference' => $transaction->reference,
                    'note' => $transaction->note,
                    'createdBy' => $transaction->createdBy ? ['name' => $transaction->createdBy->name] : null,
                    'updatedBy' => $transaction->updatedBy ? ['name' => $transaction->updatedBy->name] : null,
                    'created_at' => Carbon::parse($transaction->created_at)->format('j F, Y g:i A'),
                    'updated_at' => Carbon::parse($transaction->updated_at)->format('j F, Y g:i A'),
                ];
            });

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'organization' => $organization,
            'filters' => $request->all(),
            'permissions' => [
                'view' => auth()->user()->can('transactions.view'),
                'create' => auth()->user()->can('transactions.create'),
                'edit' => auth()->user()->can('transactions.edit'),
                'delete' => auth()->user()->can('transactions.delete'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('transactions.create')) {
            abort(403, 'You do not have permission to create transactions.');
        }
        $donors = Donor::getDropdown();
        $funds = Fund::getDropdown();

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
        if (!auth()->user()->can('transactions.create')) {
            abort(403, 'You do not have permission to create transactions.');
        }
        // Validate incoming request data (exclude txn_id)
        $validated = $request->validate([
            'donor_id' => 'nullable|exists:donors,id',
            'fund_id' => 'required|exists:funds,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:credit,debit',
            'purpose' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,bkash,card,bank,nagad,rocket',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,completed,canceled',
        ]);

        $organization_id = $request->session()->get("organization_id");

        // Get the last transaction id
        $lastTransaction = Transaction::latest('id')->first();
        $nextId = $lastTransaction ? $lastTransaction->id + 1 : 1;
        $txn_id = 'TXN' . time() . $nextId;

        // Create a new transaction record
        $transaction = Transaction::create([
            'organization_id' => $organization_id,
            'txn_id' => $txn_id,
            'donor_id' => $validated['donor_id'],
            'fund_id' => $validated['fund_id'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'purpose' => $validated['purpose'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'],
            'note' => $validated['note'],
            'status' => $validated['status'] ?? 'pending',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('transactions.index')->with([
            'success' => 'Transaction created successfully!'
        ]);
    }



    public function createIncome()
    {
        if (!auth()->user()->can('transactions.create')) {
            abort(403, 'You do not have permission to create transactions.');
        }
        $donors = Donor::getDropdown();
        $funds = Fund::getDropdown();

        return Inertia::render('Transactions/IncomeCreate', [
            'donors' => $donors,
            'funds' => $funds
        ]);
    }

    public function storeIncome(Request $request)
    {
        if (!auth()->user()->can('transactions.create')) {
            abort(403, 'You do not have permission to create transactions.');
        }
        return $this->storeTransaction($request, 'credit');
    }

    public function createExpense()
    {
        if (!auth()->user()->can('transactions.create')) {
            abort(403, 'You do not have permission to create transactions.');
        }
        $donors = Donor::getDropdown();
        $funds = Fund::getDropdown();

        return Inertia::render('Transactions/ExpenseCreate', [
            'donors' => $donors,
            'funds' => $funds
        ]);
    }

    public function storeExpense(Request $request)
    {
        if (!auth()->user()->can('transactions.create')) {
            abort(403, 'You do not have permission to create transactions.');
        }
        return $this->storeTransaction($request, 'debit');
    }

    private function storeTransaction(Request $request, string $type)
    {
        if (!auth()->user()->can('transactions.create')) {
            abort(403, 'You do not have permission to create transactions.');
        }
        // Validate incoming request data (exclude txn_id)
        $validated = $request->validate([
            'donor_id' => 'nullable|exists:donors,id',
            'fund_id' => 'required|exists:funds,id',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,bkash,card,bank,nagad,rocket',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,completed,canceled',
        ]);

        $organization_id = $request->session()->get("organization_id");

        // Get the last transaction id
        $lastTransaction = Transaction::latest('id')->first();
        $nextId = $lastTransaction ? $lastTransaction->id + 1 : 1;
        $txn_id = 'TXN' . time() . $nextId;

        // Create a new transaction record
        $transaction = Transaction::create([
            'organization_id' => $organization_id,
            'txn_id' => $txn_id,
            'donor_id' => $validated['donor_id'],
            'fund_id' => $validated['fund_id'],
            'amount' => $validated['amount'],
            'type' => $type, // Set from method parameter
            'purpose' => $validated['purpose'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'],
            'note' => $validated['note'],
            'status' => $validated['status'] ?? 'pending',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('transactions.index')->with([
            'success' => 'Transaction created successfully!',
            'click_recipt' => true
        ]);
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
        if (!auth()->user()->can('transactions.edit')) {
            abort(403, 'You do not have permission to edit transactions.');
        }
        $validated = $request->validate([
            'donor_id' => 'nullable|exists:donors,id',
            'fund_id' => 'required|exists:funds,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:credit,debit',
            'purpose' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,bkash,bank',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,completed,canceled',
        ]);

        $transaction->update(array_merge($validated, [
            'updated_by' => auth()->id(),
        ]));

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        if (!auth()->user()->can('transactions.delete')) {
            abort(403, 'You do not have permission to delete transactions.');
        }
        // Check if the transaction exists
        if (!$transaction) {
            return redirect()->back()->with('error', 'Transaction not found');
        }

        try {
            $transaction->delete();
            return redirect()->route('transactions.index')
                ->with('success', 'Transaction deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }
}
