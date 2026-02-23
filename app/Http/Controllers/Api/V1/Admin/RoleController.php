<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        if ($request->has('all')) {
            return response()->json([
                'data' => $query->get()
            ]);
        }

        return response()->json(
            $query->paginate($request->get('per_page', 10))
        );
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        return response()->json([
            'data' => $role->load('permissions')
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role->load('permissions')
        ], 201);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role->load('permissions')
        ]);
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        if (in_array($role->slug, ['super-admin', 'admin', 'user'])) {
            return response()->json([
                'message' => 'Cannot delete system roles.'
            ], 403);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }
}
