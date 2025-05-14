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

        $permissions = Permission::where('organization_id', $organization_id)
            ->orderBy('name')
            ->get()
            ->groupBy(function ($item) {
                return explode('.', $item->name)[0]; // Group by resource (funds, donors, etc.)
            });

        return Inertia::render('RolesPermissions/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'availablePermissions' => $permissions->flatten()->pluck('name')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,organization_id,'.$request->session()->get('organization_id'),
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name,organization_id,'.$request->session()->get('organization_id')
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

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id.',id,organization_id,'.$request->session()->get('organization_id'),
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name,organization_id,'.$request->session()->get('organization_id')
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
                'updated_by' => auth()->id()
            ]);

            $role->syncPermissions($request->permissions);
        });

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Prevent deletion of admin role
        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
