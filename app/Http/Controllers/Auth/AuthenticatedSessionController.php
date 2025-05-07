<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user(); // Logged-in user

        // Load the organization relation (if not already eager-loaded)
        $organization = $user->organization()->first();

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
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
