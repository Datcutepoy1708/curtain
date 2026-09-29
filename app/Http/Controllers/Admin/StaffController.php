<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'manager', 'technician', 'sales', 'staff'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $staffMembers = $query->paginate(15)->withQueryString();
        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create()
    {
        $permissionGroups = User::availablePermissions();
        return view('admin.staff.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,technician,sales,staff',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'status' => 'nullable|in:active,banned',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $request->input('status', 'active');
        $validated['permissions'] = $validated['role'] === 'admin' ? [] : ($request->input('permissions') ?? []);

        User::create($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Đã thêm tài khoản nhân sự & phân quyền thành công.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $permissionGroups = User::availablePermissions();
        return view('admin.staff.edit', compact('user', 'permissionGroups'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,technician,sales,staff',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'status' => 'required|in:active,banned',
        ]);

        // Prevent self-demotion or self-banning
        if ($user->id === auth()->id()) {
            if ($validated['status'] !== 'active') {
                return redirect()->back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.')->withInput();
            }
            if ($validated['role'] !== 'admin') {
                return redirect()->back()->with('error', 'Bạn không thể tự hạ quyền Admin của chính mình.')->withInput();
            }
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['permissions'] = $validated['role'] === 'admin' ? [] : ($request->input('permissions') ?? []);

        $user->update($validated);

        return redirect()->route('admin.staff.index')->with('success', "Đã cập nhật tài khoản và quyền hạn của {$user->name}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể xóa tài khoản của chính mình.');
        }

        $user->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Đã xóa tài khoản nhân sự thành công.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Không thể tự khóa tài khoản của chính mình.');
        }

        $user->status = $user->status === 'active' ? 'banned' : 'active';
        $user->save();

        return redirect()->back()->with('success', "Đã thay đổi trạng thái tài khoản {$user->name}.");
    }
}
