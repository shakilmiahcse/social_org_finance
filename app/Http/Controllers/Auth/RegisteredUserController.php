<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(Request $request): Response
    {
        $step = $request->query('step', 1);
        $step = in_array($step, [1, 2]) ? (int)$step : 1;

        return Inertia::render('auth/Register', [
            'step' => $step,
            'orgData' => $request->session()->get('registration.org_data', [])
        ]);
    }

    /**
     * Handle organization registration (step 1)
     */
    public function storeOrganization(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_email' => 'required|string|email|max:255|unique:organizations,email',
            'org_phone' => 'nullable|string|max:20',
            'org_address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $request->session()->put('registration.org_data', $validated);
            DB::commit();

            return redirect()->route('register', ['step' => 2]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Organization registration failed: ' . $e->getMessage());

            return back()->withErrors([
                'message' => 'Failed to save organization information. Please try again.'
            ]);
        }
    }

    /**
     * Handle user registration (step 2)
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $userData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $orgData = $request->session()->get('registration.org_data');
        if (!$orgData) {
            return back()->withErrors([
                'message' => 'Organization information is missing. Please start over.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Create organization
            $organization = Organization::create([
                'name' => $orgData['org_name'],
                'email' => $orgData['org_email'],
                'phone' => $orgData['org_phone'] ?? null,
                'address' => $orgData['org_address'] ?? null,
                'timezone' => 'Asia/Dhaka',
                'currency' => 'BDT',
            ]);

            // Create user with organization relationship
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'organization_id' => $organization->id,
            ]);

            // Create default roles for this organization
            $adminRole = Role::withoutGlobalScope('organization')->create([
                'name' => 'admin',
                'organization_id' => $organization->id,
                'guard_name' => 'web'
            ]);

            $managerRole = Role::withoutGlobalScope('organization')->create([
                'name' => 'manager',
                'organization_id' => $organization->id,
                'guard_name' => 'web'
            ]);

            $userRole = Role::withoutGlobalScope('organization')->create([
                'name' => 'user',
                'organization_id' => $organization->id,
                'guard_name' => 'web'
            ]);

            // Assign permissions to roles
            $adminRole->givePermissionTo(Permission::get());

            $managerRole->givePermissionTo(
                Permission::whereIn('name', ['funds.view', 'donors.view', 'transactions.view', 'adjustments.view'])
                    ->get()
            );

            // Assign admin role to the registered user
            $user->assignRole($adminRole);

            if ($organization) {
                // Store each organization attribute individually in the session
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
            }

            DB::commit();

            // Clean up and finalize
            $request->session()->forget('registration.org_data');
            event(new Registered($user));
            Auth::login($user);

            return to_route('dashboard');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User registration failed: ' . $e->getMessage());

            return back()->withErrors([
                'message' => 'Registration failed. Please try again. ' .
                    ($e instanceof \Illuminate\Database\QueryException ?
                        'Database error occurred.' : $e->getMessage())
            ]);
        }
    }
}
