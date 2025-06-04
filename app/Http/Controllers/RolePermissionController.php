<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;

class RolePermissionController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('roles.view')) {
            abort(403, 'You do not have permission to view roles and permissions.');
        }
        $organization_id = request()->session()->get("organization_id");

        $roles = Role::with(['permissions'])
            ->where('organization_id', $organization_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name'),
                    'created_at' => $role->created_at->format('j F, Y g:i A'),
                    'updated_at' => $role->updated_at->format('j F, Y g:i A'),
                ];
            });


        $permissions = Permission::orderBy('name')
            ->get()
            ->groupBy(function ($item) {
                return explode('.', $item->name)[0]; // Group by resource (funds, donors, etc.)
            });

        return Inertia::render('RolesPermissions/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'availablePermissions' => $permissions->flatten()->pluck('name'),
            'can' => [
                'view' => auth()->user()->can('roles.view'),
                'create' => auth()->user()->can('roles.create'),
                'edit' => auth()->user()->can('roles.edit'),
                'delete' => auth()->user()->can('roles.delete'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'You do not have permission to create roles.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,organization_id,' . $request->session()->get('organization_id'),
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
                'organization_id' => $request->session()->get('organization_id'),
                'created_by' => auth()->id()
            ]);

            if ($request->permissions) {
                $role->syncPermissions($request->permissions);
            }
        });

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->can('roles.edit')) {
            abort(403, 'You do not have permission to edit roles.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . ',id,organization_id,' . $request->session()->get('organization_id'),
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        DB::transaction(function () use ($request, $role) {
            // শুধু মাত্র আপডেট করা ফিল্ডগুলো সেট করুন
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->permissions);
        });

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->can('roles.delete')) {
            abort(403, 'You do not have permission to delete roles.');
        }
        // Prevent deletion of admin role
        if ($role->name === 'admin') {
            return redirect()->back()
                ->with('error', 'Admin role cannot be deleted.');
        }

        // Check if any users are assigned to this role
        if ($role->users()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete role because it is assigned to users.');
        }

        try {
            $role->delete();
            return redirect()->back()
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
}
