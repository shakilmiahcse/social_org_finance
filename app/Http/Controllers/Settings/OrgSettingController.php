<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrgSettingController extends Controller
{
    public function edit()
    {
        $organization_id = request()->session()->get("organization_id");
        return Inertia::render('settings/org', [
            'organization' => Organization::find($organization_id),
            'timezones' => \DateTimeZone::listIdentifiers(),
            'currencies' => ['USD', 'EUR', 'BDT', 'GBP'],
        ]);
    }

    public function update(Request $request)
    {
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
            // Store file on the 'public' disk inside 'logos' folder (no 'public/' prefix)
            $path = $request->file('logo_path')->store('logos', 'public');
            $data['logo_path'] = Storage::url($path);  // e.g. /storage/logos/filename.png

            // Delete old logo if exists
            if ($organization->logo_path) {
                // Remove '/storage' prefix to get storage path
                $oldPath = str_replace('/storage/', '', $organization->logo_path);
                Storage::disk('public')->delete($oldPath);
            }
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
