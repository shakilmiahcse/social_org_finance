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

        // Verify the donor belongs to the organization
        $organization_id = request()->session()->get("organization_id");
        if ($donor->organization_id !== $organization_id) {
            abort(403, 'You do not have permission to view this donor.');
        }

        // Get transactions (assuming donations are 'credit' type)
        $transactions = Transaction::where('donor_id', $donor->id)
            ->with(['createdBy', 'fund'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($txn) {
                return [
                    'id' => $txn->id,
                    'txn_no' => $txn->txn_id ?? $txn->id, // Fallback if txn_id is not present
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

        // Calculate summary
        $summary = [
            'total_donated' => Transaction::where('donor_id', $donor->id)
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_raised' => Transaction::where('donor_id', $donor->id)
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_transactions' => Transaction::where('donor_id', $donor->id)->count(),
        ];

        // Get all donors for dropdown with total donated in brackets
        $donors = Donor::where('donors.organization_id', $organization_id)
            ->select(
                'donors.id',
                'donors.name',
                'donors.phone',
            )
            ->groupBy('donors.id', 'donors.name', 'donors.phone')
            ->orderBy('donors.name', 'asc')
            ->get()
            ->map(function ($donor) {
                return [
                    'id' => $donor->id,
                    'name' => $donor->name . ($donor->phone ? ' (' . $donor->phone . ')' : '')
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
