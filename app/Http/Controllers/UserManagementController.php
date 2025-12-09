<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(10);

        return Inertia::render('UserManagement/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return Inertia::render('UserManagement/Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        return Redirect::route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions', 'permissions']);

        return Inertia::render('UserManagement/Users/Show', [
            'user' => $user,
            'currentUserId' => auth()->id(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $user->load('roles');

        return Inertia::render('UserManagement/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        $user->syncRoles($request->roles ?? []);

        return Redirect::route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deletion of the current user
        if (auth()->id() === $user->id) {
            return Redirect::route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return Redirect::route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Assign roles to user via AJAX
     */
    public function assignRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->syncRoles($request->roles);

        return response()->json(['message' => 'Roles assigned successfully.']);
    }

    /**
     * Remove role from user via AJAX
     */
    public function removeRole(Request $request, User $user, Role $role)
    {
        $user->removeRole($role);

        return response()->json(['message' => 'Role removed successfully.']);
    }

    /**
     * Assign direct permission to user via AJAX
     */
    public function assignPermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $user->syncPermissions($request->permissions);

        return response()->json(['message' => 'Permissions assigned successfully.']);
    }

    /**
     * Get user's effective permissions (from roles + direct permissions)
     */
    public function getEffectivePermissions(User $user)
    {
        $user->load('roles.permissions', 'permissions');

        $rolePermissions = $user->roles->flatMap->permissions->unique('id');
        $directPermissions = $user->permissions;
        $allPermissions = $rolePermissions->merge($directPermissions)->unique('id');

        return response()->json([
            'role_permissions' => $rolePermissions,
            'direct_permissions' => $directPermissions,
            'all_permissions' => $allPermissions,
        ]);
    }
}