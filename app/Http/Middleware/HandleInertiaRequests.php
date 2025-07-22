<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected function isSuperAdmin($user): bool
    {
        return $user->email === config('app.super_admin_email');
    }
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();
        $organization = $user?->organization;

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user ? [
                    ...$user->toArray(),
                    'roles' => $user->getRoleNames(),
                    'permissions' => $this->isSuperAdmin($user)
                        ? collect(\Spatie\Permission\Models\Permission::pluck('name'))
                        : $user->getAllPermissions()->pluck('name'),
                    'can' => $this->isSuperAdmin($user)
                        ? \Spatie\Permission\Models\Permission::pluck('name')->toArray()
                        : $user->getAllPermissions()->pluck('name')->toArray(),
                    'is_super_admin' => $this->isSuperAdmin($user),
                ] : null,
                'organization' => $organization ? [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'email' => $organization->email,
                    'phone' => $organization->phone,
                    'address' => $organization->address,
                    'logo_path' => $organization->logo_path,
                    'website' => $organization->website,
                    'timezone' => $organization->timezone,
                    'currency' => $organization->currency,
                    'is_active' => $organization->is_active,
                    'slogan' => $organization->slogan,
                    'created_at' => $organization->created_at,
                    'updated_at' => $organization->updated_at,
                    'common_setting' => $organization->common_setting,
                ] : null,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
