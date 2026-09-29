<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::with('staff')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%");
            });
        }

        $consultations = $query->paginate(15)->withQueryString();
        $staffMembers = User::where('role', '!=', 'customer')->orderBy('name')->get();

        return view('admin.consultations.index', compact('consultations', 'staffMembers'));
    }

    public function show($id)
    {
        $consultation = Consultation::with([
            'staff',
            'windows.product',
            'quotations.items',
            'currentQuotation.items',
            'currentQuotation.order'
        ])->findOrFail($id);

        $staffMembers = User::where('role', '!=', 'customer')
            ->orderByRaw("CASE 
                WHEN role = 'technician' THEN 1 
                WHEN role = 'staff' THEN 2 
                WHEN role = 'sales' THEN 3 
                WHEN role = 'manager' THEN 4 
                ELSE 5 END")
            ->orderBy('name')
            ->get();
        $products = \App\Models\Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.consultations.show', compact('consultation', 'staffMembers', 'products'));
    }

    public function updateStatus(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,assigned,surveying,quoted,completed,cancelled',
            'staff_id' => 'nullable|exists:users,id',
            'quotation_amount' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $oldValues = [
            'status' => $consultation->status,
            'staff_id' => $consultation->staff_id,
        ];

        $consultation->update($validated);

        $actionType = ($oldValues['staff_id'] != $consultation->staff_id) ? 'assign' : 'update_status';
        $desc = "Cập nhật điều phối lịch hẹn #{$consultation->code}: Trạng thái '{$consultation->status}'";
        if ($consultation->staff) {
            $desc .= " — Phân công: {$consultation->staff->name}";
        }

        \App\Models\AuditLog::record(
            $actionType,
            'consultations',
            $desc,
            $consultation->code,
            $oldValues,
            $validated
        );

        return redirect()->back()->with('success', "Cập nhật trạng thái lịch hẹn [{$consultation->code}] thành công!");
    }
}
