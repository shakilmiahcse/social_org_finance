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

        return Inertia::render('settings/Org', [
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
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:max_width=500,max_height=500',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'timezone' => 'nullable|string|in:' . implode(',', \DateTimeZone::listIdentifiers()),
            'currency' => 'nullable|string|in:USD,EUR,BDT,GBP',
            'slogan' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $organization = $user->organization()->first();
        $data = $request->except('logo_path');

        if ($request->hasFile('logo_path')) {
            $logo = $request->file('logo_path');
            $filename = Str::uuid() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('logos', $filename, 'public');

            if ($organization->logo_path) {
                Storage::disk('public')->delete($organization->logo_path);
            }

            $data['logo_path'] = $path;
        }

        $organization->update($data);

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
            'organization_logo_path' => $organization->logo_path ? Storage::url($organization->logo_path) : null,
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

        // Define English default settings
        $defaultSettings = [
            'credit' => [
                'header' => [
                    'title' => 'Donation Receipt',
                    'subtitle' => 'Thank you for your generous support!',
                    'color' => '#16a34a',
                    'icon' => 'hand-holding-heart',
                ],
                'body' => [
                    'watermark_text' => 'RECEIPT',
                    'watermark_color' => '#22c55e',
                    'background_color' => '#f0fdf4',
                    'transaction_style' => '#dcfce7',
                ],
                'footer' => [
                    'message' => 'Your support helps us continue our mission.',
                    'note' => 'This receipt is an official document for your records.',
                ],
                'labels' => [
                    'amount' => 'Donation Amount',
                    'date' => 'Date',
                    'method' => 'Donation Method',
                    'donor' => 'Donor Name',
                    'fund' => 'Fund',
                    'purpose' => 'Purpose',
                ],
            ],
            'debit' => [
                'header' => [
                    'title' => 'Payment Receipt',
                    'subtitle' => 'Thank you for your payment!',
                    'color' => '#2563eb',
                    'icon' => 'receipt',
                ],
                'body' => [
                    'watermark_text' => 'PAYMENT',
                    'watermark_color' => '#3b82f6',
                    'background_color' => '#eff6ff',
                    'transaction_style' => '#dbeafe',
                ],
                'footer' => [
                    'message' => 'This payment supports our welfare activities.',
                    'note' => 'This receipt is an official document for your records.',
                ],
                'labels' => [
                    'amount' => 'Payment Amount',
                    'date' => 'Date',
                    'method' => 'Payment Method',
                    'donor' => 'Recipient Name',
                    'fund' => 'Fund',
                    'purpose' => 'Purpose',
                ],
            ],
        ];

        $commonSettings = $organization->common_setting ?? [];
        if (is_string($commonSettings)) {
            $commonSettings = json_decode($commonSettings, true, 512, JSON_UNESCAPED_UNICODE) ?? [];
        }

        // Merge database settings with defaults, prioritizing database values
        $receiptSettings = array_replace_recursive(
            $defaultSettings,
            $commonSettings['receipt'] ?? []
        );

        return Inertia::render('settings/Receipt', [
            'receiptSettings' => $receiptSettings,
            'organization' => [
                'name' => $organization->name,
                'currency' => $organization->currency ?? 'BDT',
                'logo_path' => $organization->logo_path ? Storage::url($organization->logo_path) : null,
            ],
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
            'receipt.credit.header.color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'receipt.credit.header.icon' => 'nullable|string|in:' . implode(',', array_column($this->getIconOptions(), 'value')),
            'receipt.credit.body.watermark_text' => 'nullable|string|max:255',
            'receipt.credit.body.watermark_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'receipt.credit.body.background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'receipt.credit.body.transaction_style' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
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
            'receipt.debit.header.color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'receipt.debit.header.icon' => 'nullable|string|in:' . implode(',', array_column($this->getIconOptions(), 'value')),
            'receipt.debit.body.watermark_text' => 'nullable|string|max:255',
            'receipt.debit.body.watermark_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'receipt.debit.body.background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'receipt.debit.body.transaction_style' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
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
        $commonSettings = $organization->common_setting ?? [];
        if (is_string($commonSettings)) {
            $commonSettings = json_decode($commonSettings, true, 512, JSON_UNESCAPED_UNICODE) ?? [];
        }

        $commonSettings['receipt'] = $request->receipt;
        $organization->common_setting = json_encode($commonSettings, JSON_UNESCAPED_UNICODE);
        $organization->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($organization)
            ->withProperties([
                'changes' => ['receipt' => $request->receipt],
                'old' => ['receipt' => $organization->getOriginal('common_setting')['receipt'] ?? []]
            ])
            ->log('updated receipt settings');

        return back()->with('success', 'Receipt settings updated successfully');
    }

    private function getIconOptions()
    {
        return [
            ['value' => 'hand-holding-heart', 'label' => 'Hand Holding Heart'],
            ['value' => 'receipt', 'label' => 'Receipt'],
            ['value' => 'donate', 'label' => 'Donate'],
            ['value' => 'hands-helping', 'label' => 'Hands Helping'],
            ['value' => 'gift', 'label' => 'Gift'],
            ['value' => 'building', 'label' => 'Building'],
            ['value' => 'university', 'label' => 'University'],
            ['value' => 'church', 'label' => 'Church'],
            ['value' => 'money-bill-wave', 'label' => 'Money Bill Wave'],
            ['value' => 'credit-card', 'label' => 'Credit Card'],
            ['value' => 'wallet', 'label' => 'Wallet'],
            ['value' => 'handshake', 'label' => 'Handshake'],
        ];
    }
}
