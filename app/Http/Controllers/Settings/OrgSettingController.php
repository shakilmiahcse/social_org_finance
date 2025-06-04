<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Str;

class OrgSettingController extends Controller
{
    public function edit()
    {
        if (!auth()->user()->can('settings.view')) {
            abort(403, 'You do not have permission to view organization settings.');
        }
        $user = request()->user();
        $organization = $user->organization()->first();

        return Inertia::render('settings/org', [
            'organization' => $organization,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'currencies' => ['USD', 'EUR', 'BDT', 'GBP'],
            'can' => [
                'view' => auth()->user()->can('settings.view'),
                'update' => auth()->user()->can('settings.update'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        if (!auth()->user()->can('settings.update')) {
            abort(403, 'You do not have permission to update organization settings.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:50|dimensions:max_width=500,max_height=500',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'timezone' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
            'slogan' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $organization = $user->organization()->first();
        $data = $request->except('logo_path');

        if ($request->hasFile('logo_path')) {
            $logo = $request->file('logo_path');
            $filename = Str::random(40) . '.' . $logo->getClientOriginalExtension();
            $destinationPath = public_path('uploads/logos');

            // Ensure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $logo->move($destinationPath, $filename);

            // Delete old logo if exists
            if ($organization->logo_path && file_exists(public_path($organization->logo_path))) {
                @unlink(public_path($organization->logo_path));
            }

            $data['logo_path'] = 'uploads/logos/' . $filename;
        }

        $organization->update($data);

        session([
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'organization_email' => $organization->email,
            'organization_phone' => $organization->phone,
            'organization_address' => $organization->address,
            'organization_logo_path' => $organization->logo_path,
            'organization_website' => $organization->website,
            'organization_timezone' => $organization->timezone,
            'organization_currency' => $organization->currency,
            'organization_is_active' => $organization->is_active,
            'organization_slogan' => $organization->slogan,
        ]);

        return back()->with('success', 'Organization settings updated successfully');
    }

}
