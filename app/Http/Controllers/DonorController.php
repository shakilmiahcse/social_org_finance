<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        if (!auth()->user()->can('donors.view')) {
            abort(403, 'You do not have permission to view donors.');
        }
        $organization_id = request()->session()->get("organization_id");

        $donors = Donor::with(['createdBy', 'updatedBy'])->where('organization_id', $organization_id)->orderBy('created_at', 'desc')->get()->map(function ($donor) {
            return [
                'id'          => $donor->id,
                'name'        => $donor->name,
                'email'       => $donor->email,
                'phone'       => $donor->phone,
                'blood_group' => $donor->blood_group,
                'address'     => $donor->address,
                'createdBy'   => $donor->createdBy ? ['name' => $donor->createdBy->name] : null,
                'updatedBy'   => $donor->updatedBy ? ['name' => $donor->updatedBy->name] : null,
                'created_at'  => Carbon::parse($donor->created_at)->format('j F, Y g:i A'),
                'updated_at'  => Carbon::parse($donor->updated_at)->format('j F, Y g:i A'),
            ];
        });

        return Inertia::render('Donors/Index', [
            'donors' => $donors,
            'permissions' => [
                'view' => auth()->user()->can('donors.view'),
                'edit' => auth()->user()->can('donors.edit'),
                'delete' => auth()->user()->can('donors.delete'),
                'create' => auth()->user()->can('donors.create'),
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
        if (!auth()->user()->can('donors.create')) {
            abort(403, 'You do not have permission to create donors.');
        }
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:donors,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'blood_group' => 'nullable|string|max:5',
            ]);

            $organization_id = $request->session()->get("organization_id");
            $validated['organization_id'] = $organization_id;
            $validated['created_by'] = auth()->id();
            $donor = Donor::create($validated);

            return response()->json([
                'success' => true,
                'donor' => [
                    'id' => $donor->id,
                    'name' => $donor->name
                ],
                'message' => 'Donor created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdown()
    {
        if (!auth()->user()->can('donors.view')) {
            abort(403, 'You do not have permission to view donors.');
        }
        $organization_id = request()->session()->get("organization_id");
        try {
            $donors = Donor::where('organization_id', $organization_id)->latest()
                ->get()
                ->map(function ($donor) {
                    return [
                        'id' => $donor->id,
                        'name' => $donor->name,
                        'phone' => $donor->phone
                    ];
                });

            return response()->json([
                'success' => true,
                'donors' => $donors
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
    public function show(Donor $donor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donor $donor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donor $donor)
    {
        if (!auth()->user()->can('donors.edit')) {
            abort(403, 'You do not have permission to edit donors.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:5'
        ]);

        $validated['updated_by'] = auth()->id();
        $donor->update($validated);

        return redirect()->back()->with('success', 'Donor updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donor $donor)
    {
        if (!auth()->user()->can('donors.delete')) {
            abort(403, 'You do not have permission to delete donors.');
        }
        $donor->delete();
        return redirect()->route('donors.index')->with('success', 'Donor deleted successfully.');
    }

    public function history(Donor $donor)
    {
        if (!auth()->user()->can('donors.view')) {
            abort(403, 'You do not have permission to view donor history.');
        }

        $organization_id = request()->session()->get("organization_id");
        if ($donor->organization_id !== $organization_id) {
            abort(403, 'You do not have permission to view this donor.');
        }

        // ✅ একবার Transaction query তৈরি
        $baseQuery = Transaction::where('donor_id', $donor->id);

        // ✅ লেনদেন তালিকা (transactions)
        $transactions = (clone $baseQuery)
            ->with(['createdBy', 'fund'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($txn) {
                return [
                    'id' => $txn->id,
                    'txn_no' => $txn->txn_id ?? $txn->id,
                    'type' => $txn->type,
                    'amount' => $txn->amount,
                    'fund' => $txn->fund ? ['name' => $txn->fund->name] : null,
                    'purpose' => $txn->purpose,
                    'payment_method' => $txn->payment_method,
                    'reference' => $txn->reference,
                    'status' => $txn->status,
                    'created_at' => $txn->created_at,
                    'createdBy' => $txn->createdBy ? ['name' => $txn->createdBy->name] : null,
                ];
            });

        // ✅ Summary হিসাব clone ব্যবহার করে
        $summary = [
            'total_donated' => (clone $baseQuery)
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),

            'total_raised' => (clone $baseQuery)
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->sum('amount'),

            'total_transactions' => (clone $baseQuery)->count(),
        ];

        // ✅ Donor তালিকা (dropdown)
        $donors = Donor::where('organization_id', $organization_id)
            ->select('id', 'name', 'phone', 'address')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($donor) {
                $displayName = $donor->name;
                if ($donor->phone) {
                    $displayName .= " ({$donor->phone})";
                }

                return [
                    'id' => $donor->id,
                    'name' => $donor->name,
                    'phone' => $donor->phone,
                    'address' => $donor->address,
                    'display_name' => $displayName,
                ];
            });

        return Inertia::render('Donors/History', [
            'donors' => $donors,
            'transactions' => $transactions,
            'summary' => $summary,
            'current_donor_id' => $donor->id,
        ]);
    }

}
