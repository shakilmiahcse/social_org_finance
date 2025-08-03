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

        // Default settings for both credit and debit receipts
        $defaultSettings = [
            'credit' => [
                'header' => [
                    'title' => 'অনুদান রসিদ',
                    'subtitle' => 'আপনার উদার সহায়তার জন্য ধন্যবাদ!',
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
                    'message' => 'আপনার সহযোগিতা আমাদের কাজ অব্যাহত রাখার অনুপ্রেরণা জোগায়।',
                    'note' => 'এই রসিদটি সংরক্ষণের জন্য একটি অফিসিয়াল ডকুমেন্ট।',
                ],
                'labels' => [
                    'amount' => 'Transaction Amount',
                    'date' => 'তারিখ',
                    'method' => 'অনুদান মাধ্যম',
                    'donor' => 'দাতার নাম',
                    'fund' => 'তহবিল',
                    'purpose' => 'উদ্দেশ্য',
                ],
            ],
            'debit' => [
                'header' => [
                    'title' => 'পেমেন্ট রসিদ',
                    'subtitle' => 'আপনার পেমেন্টের জন্য ধন্যবাদ!',
                    'color' => 'bg-gradient-to-r from-blue-600 to-indigo-700',
                    'icon' => 'receipt',
                ],
                'body' => [
                    'watermark_text' => 'PAYMENT',
                    'watermark_color' => 'text-blue-500/10',
                    'background_color' => 'bg-blue-50',
                    'transaction_style' => 'bg-blue-100/50',
                ],
                'footer' => [
                    'message' => 'এই অর্থায়ন কল্যাণমূলক কার্যক্রমের উন্নয়নে ব্যবহৃত হয়েছে।',
                    'note' => 'এই রসিদটি সংরক্ষণের জন্য একটি অফিসিয়াল ডকুমেন্ট।',
                ],
                'labels' => [
                    'amount' => 'Transaction Amount',
                    'date' => 'তারিখ',
                    'method' => 'পেমেন্ট মাধ্যম',
                    'donor' => 'উত্তোলনকারীর নাম',
                    'fund' => 'তহবিল',
                    'purpose' => 'উদ্দেশ্য',
                ],
            ],
        ];

        // Merge with existing settings
        $receiptSettings = array_merge_recursive(
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
            'receipt.credit.header.title' => 'nullable|string|max:255',
            'receipt.credit.header.subtitle' => 'nullable|string|max:255',
            'receipt.credit.header.color' => 'nullable|string|max:255',
            'receipt.credit.header.icon' => 'nullable|string|max:50',
            'receipt.credit.body.watermark_text' => 'nullable|string|max:255',
            'receipt.credit.body.watermark_color' => 'nullable|string|max:255',
            'receipt.credit.body.background_color' => 'nullable|string|max:255',
            'receipt.credit.body.transaction_style' => 'nullable|string|max:255',
            'receipt.credit.footer.message' => 'nullable|string|max:500',
            'receipt.credit.footer.note' => 'nullable|string|max:500',
            'receipt.credit.labels.amount' => 'nullable|string|max:100',
            'receipt.credit.labels.date' => 'nullable|string|max:100',
            'receipt.credit.labels.method' => 'nullable|string|max:100',
            'receipt.credit.labels.donor' => 'nullable|string|max:100',
            'receipt.credit.labels.fund' => 'nullable|string|max:100',
            'receipt.credit.labels.purpose' => 'nullable|string|max:100',

            'receipt.debit.header.title' => 'nullable|string|max:255',
            'receipt.debit.header.subtitle' => 'nullable|string|max:255',
            'receipt.debit.header.color' => 'nullable|string|max:255',
            'receipt.debit.header.icon' => 'nullable|string|max:50',
            'receipt.debit.body.watermark_text' => 'nullable|string|max:255',
            'receipt.debit.body.watermark_color' => 'nullable|string|max:255',
            'receipt.debit.body.background_color' => 'nullable|string|max:255',
            'receipt.debit.body.transaction_style' => 'nullable|string|max:255',
            'receipt.debit.footer.message' => 'nullable|string|max:500',
            'receipt.debit.footer.note' => 'nullable|string|max:500',
            'receipt.debit.labels.amount' => 'nullable|string|max:100',
            'receipt.debit.labels.date' => 'nullable|string|max:100',
            'receipt.debit.labels.method' => 'nullable|string|max:100',
            'receipt.debit.labels.donor' => 'nullable|string|max:100',
            'receipt.debit.labels.fund' => 'nullable|string|max:100',
            'receipt.debit.labels.purpose' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        $organization = $user->organization()->first();

        // Initialize common_setting as array if it's null or a string
        $commonSettings = $organization->common_setting ?? [];

        // Ensure it's an array (in case it was stored as JSON string)
        if (is_string($commonSettings)) {
            $commonSettings = json_decode($commonSettings, true) ?? [];
        }

        // Safely merge the new receipt settings
        $commonSettings['receipt'] = $request->receipt;

        // Save the updated settings
        $organization->common_setting = $commonSettings;
        $organization->save();

        return back()->with('success', 'Receipt settings updated successfully');
    }

}
