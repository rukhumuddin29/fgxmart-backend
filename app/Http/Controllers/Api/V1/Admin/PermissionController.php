<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            return response()->json([
                'data' => Permission::all()
            ]);
        }

        return response()->json(
            Permission::paginate($request->get('per_page', 10))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Permission updated successfully',
            'data' => $permission
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully'
        ]);
    }
}
