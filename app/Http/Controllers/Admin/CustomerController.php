<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount(['orders', 'consultations'])
            ->latest();

        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $customers = $query->paginate(15)->withQueryString();

        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')->where('status', 'active')->count();

        return view('admin.customers.index', compact('customers', 'totalCustomers', 'activeCustomers'));
    }

    public function show($id)
    {
        $customer = User::where('role', 'customer')
            ->with(['orders.items', 'consultations'])
            ->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    public function toggleStatus($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->status = $customer->status === 'active' ? 'banned' : 'active';
        $customer->save();

        return redirect()->back()->with('success', "Đã thay đổi trạng thái tài khoản {$customer->name}.");
    }
}
