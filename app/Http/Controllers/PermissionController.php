<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use App\QueryServices\PermissionQueryService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionQueryService $permissionQueryService,
        protected PermissionService $permissionService
    ) {}

    public function index()
    {
        $permissions = $this->permissionQueryService->getAllPermissions();
        return view('permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        $this->permissionService->create($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil ditambahkan.');
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
        ]);

        $this->permissionService->update($permission, $validated);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission)
    {
        $this->permissionService->delete($permission);
        return redirect()->route('permissions.index')->with('success', 'Permission berhasil dihapus.');
    }
}
