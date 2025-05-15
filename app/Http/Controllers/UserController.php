<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role as SpatieRole;

class UserController extends Controller
{
    public function index()
    {
        $organization_id = request()->session()->get("organization_id");

        $users = User::with(['roles'])
            ->where('organization_id', $organization_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                    'created_at' => $user->created_at->format('j F, Y g:i A'),
                    'updated_at' => $user->updated_at->format('j F, Y g:i A'),
                ];
            });

        $roles = Role::where('organization_id', $organization_id)
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            });

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'availableRoles' => $roles->pluck('name')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,NULL,id,organization_id,'.$request->session()->get('organization_id'),
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name,organization_id,'.$request->session()->get('organization_id')
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'organization_id' => $request->session()->get('organization_id'),
                'created_by' => auth()->id()
            ]);

            if ($request->roles) {
                $user->syncRoles($request->roles);
            }
        });

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id.',id,organization_id,'.$request->session()->get('organization_id'),
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name,organization_id,'.$request->session()->get('organization_id')
        ]);

        DB::transaction(function () use ($request, $user) {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'updated_by' => auth()->id()
            ];

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            $user->syncRoles($request->roles);
        });

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deletion of current user
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
            return redirect()->back()
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
