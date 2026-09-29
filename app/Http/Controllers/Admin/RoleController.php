<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::orderBy('id')->get();

        $selectedRoleId = (int) $request->input('role_id', $roles->firstWhere('code', 'ROLE_MANAGER')->id ?? $roles->first()->id);
        $selectedRole = Role::find($selectedRoleId) ?? $roles->first();

        $selectedPermissions = $selectedRole && is_array($selectedRole->permissions) ? $selectedRole->permissions : [];
        $permissionGroups = User::availablePermissions();

        return view('admin.roles.index', compact('roles', 'selectedRole', 'selectedPermissions', 'permissionGroups'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = $request->input('permissions', []);

        if (($role->code === 'ROLE_ADMIN' || $role->code === 'admin') && empty($permissions)) {
            return redirect()->back()->with('error', 'Không thể xóa toàn bộ quyền của Quản trị viên tối cao (ROLE_ADMIN).');
        }

        $role->permissions = $permissions;
        $role->save();

        return redirect()->route('admin.roles.index', ['role_id' => $role->id])
            ->with('success', "Đã cập nhật phân quyền thành công cho vai trò: {$role->name}");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:roles,code',
            'description' => 'nullable|string|max:255',
        ]);

        $code = strtoupper(trim($validated['code']));
        if (!str_starts_with($code, 'ROLE_')) {
            $code = 'ROLE_' . $code;
        }

        $role = Role::create([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? 'Vai trò phân quyền nghiệp vụ mới',
            'permissions' => [],
        ]);

        return redirect()->route('admin.roles.index', ['role_id' => $role->id])
            ->with('success', "Đã tạo chức vụ/vai trò mới '{$role->name}' ({$role->code}) thành công!");
    }
}
