<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%")
                  ->orWhere('target_id', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $modules = AuditLog::modules();
        $actions = AuditLog::actions();
        $users = User::where('role', '!=', 'customer')->orderBy('name')->get();

        return view('admin.audit_logs.index', compact('logs', 'modules', 'actions', 'users'));
    }

    public function show($id)
    {
        $log = AuditLog::findOrFail($id);
        return response()->json([
            'id' => $log->id,
            'user_name' => $log->user_name,
            'user_role' => $log->user_role,
            'action' => $log->action,
            'action_label' => $log->action_badge['label'],
            'module_label' => $log->module_label,
            'description' => $log->description,
            'target_id' => $log->target_id,
            'old_values' => $log->old_values,
            'new_values' => $log->new_values,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'created_at' => $log->created_at->format('d/m/Y H:i:s'),
        ]);
    }
}
