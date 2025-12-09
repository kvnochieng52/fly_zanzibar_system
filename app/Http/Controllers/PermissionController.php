<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::with('roles')->orderBy('name')->paginate(15);



        return Inertia::render('UserManagement/Permissions/IndexFixed', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('UserManagement/Permissions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
            // Note: description and category would require custom migration
        ]);

        return Redirect::route('permissions.index')->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');

        return Inertia::render('UserManagement/Permissions/Show', [
            'permission' => $permission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return Inertia::render('UserManagement/Permissions/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        return Redirect::route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Prevent deletion of permissions that are assigned to roles
        if ($permission->roles()->exists()) {
            return Redirect::route('permissions.index')->with('error', 'Cannot delete permission that is assigned to roles.');
        }

        $permission->delete();

        return Redirect::route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    /**
     * Get all permissions grouped by category for API
     */
    public function getPermissionsByCategory()
    {
        $permissions = Permission::orderBy('name')->get();

        // Group permissions by category (extracted from name)
        $grouped = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'general';
        });

        return response()->json($grouped);
    }

    /**
     * Bulk create permissions based on common patterns
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'resource' => ['required', 'string', 'max:255'],
            'actions' => ['required', 'array'],
            'actions.*' => ['required', 'string', 'max:255'],
        ]);

        $permissions = [];
        foreach ($request->actions as $action) {
            $permissionName = $request->resource . '.' . $action;

            if (!Permission::where('name', $permissionName)->exists()) {
                $permissions[] = Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            }
        }

        return response()->json([
            'message' => count($permissions) . ' permissions created successfully.',
            'permissions' => $permissions
        ]);
    }
}
