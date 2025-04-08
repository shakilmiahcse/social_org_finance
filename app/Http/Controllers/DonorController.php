<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $donors = Donor::with(['createdBy', 'updatedBy'])->orderBy('created_at', 'desc')->get()->map(function ($donor) {
            return [
                'id'          => $donor->id,
                'name'        => $donor->name,
                'email'       => $donor->email,
                'phone'       => $donor->phone,
                'address'     => $donor->address,
                'createdBy'   => $donor->createdBy ? ['name' => $donor->createdBy->name] : null,
                'updatedBy'   => $donor->updatedBy ? ['name' => $donor->updatedBy->name] : null,
                'created_at'  => Carbon::parse($donor->created_at)->format('j F, Y g:i A'),
                'updated_at'  => Carbon::parse($donor->updated_at)->format('j F, Y g:i A'),
            ];
        });

        return Inertia::render('Donors/Index', [
            'donors' => $donors
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
            'email' => 'nullable|email|max:255|unique:donors,email',
            'phone' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = auth()->id();
        Donor::create($validated);

        return redirect()->back()->with('success', 'Donor created successfully');
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255'
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
        $donor->delete();
        return redirect()->route('donors.index')->with('success', 'Donor deleted successfully.');
    }

}
