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

        // In your update method, after the update:
        activity()
            ->causedBy(auth()->user())
            ->performedOn($organization)
            ->withProperties([
                'changes' => $organization->getChanges(),
                'old' => $organization->getOriginal()
            ])
            ->log('updated organization settings');


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

    public function receiptEdit()
    {
        if (!auth()->user()->can('settings.view')) {
            abort(403, 'You do not have permission to view organization settings.');
        }

        $user = request()->user();
        $organization = $user->organization()->first();

        // Default receipt settings
        $defaultSettings = [
            'header' => [
                'title' => 'Donation Receipt',
                'subtitle' => 'Thank you for your generous support!',
                'color' => 'bg-gradient-to-r from-green-600 to-emerald-700',
                'icon' => 'hand-holding-heart',
            ],
            'body' => [
                'watermark_text' => 'RECEIPT',
                'watermark_color' => 'text-green-500/10',
                'background_color' => 'bg-green-50',
                'transaction_style' => 'bg-green-100/50',
            ],
            'footer' => [
                'message' => 'Your contribution helps us continue our work.',
                'note' => 'This receipt is an official document for record keeping.',
            ],
            'labels' => [
                'amount' => 'Donation Amount',
                'date' => 'Date',
                'method' => 'Payment Method',
                'donor' => 'Donor Name',
                'fund' => 'Fund',
                'purpose' => 'Purpose',
            ],
        ];

        // Merge with existing settings
        $receiptSettings = array_merge(
            $defaultSettings,
            $organization->common_setting['receipt'] ?? []
        );

        return Inertia::render('settings/Receipt', [
            'receiptSettings' => $receiptSettings,
            'can' => [
                'view' => auth()->user()->can('settings.view'),
                'update' => auth()->user()->can('settings.update'),
            ],
        ]);
    }

    public function receiptUpdate(Request $request)
    {
        if (!auth()->user()->can('settings.update')) {
            abort(403, 'You do not have permission to update organization settings.');
        }

        $validated = $request->validate([
            'receipt.header.title' => 'nullable|string|max:255',
            'receipt.header.subtitle' => 'nullable|string|max:255',
            'receipt.header.color' => 'nullable|string|max:255',
            'receipt.header.icon' => 'nullable|string|max:50',
            'receipt.body.watermark_text' => 'nullable|string|max:255',
            'receipt.body.watermark_color' => 'nullable|string|max:255',
            'receipt.body.background_color' => 'nullable|string|max:255',
            'receipt.body.transaction_style' => 'nullable|string|max:255',
            'receipt.footer.message' => 'nullable|string|max:500',
            'receipt.footer.note' => 'nullable|string|max:500',
            'receipt.labels.amount' => 'nullable|string|max:100',
            'receipt.labels.date' => 'nullable|string|max:100',
            'receipt.labels.method' => 'nullable|string|max:100',
            'receipt.labels.donor' => 'nullable|string|max:100',
            'receipt.labels.fund' => 'nullable|string|max:100',
            'receipt.labels.purpose' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        $organization = $user->organization()->first();

        // Get existing common settings
        $commonSettings = $organization->common_setting ?? [];

        // Update only receipt settings
        $commonSettings['receipt'] = $request->receipt;

        $organization->common_setting = $commonSettings;
        $organization->save();

        return back()->with('success', 'Receipt settings updated successfully');
    }

}
