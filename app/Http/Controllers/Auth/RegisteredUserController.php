<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
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
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
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
