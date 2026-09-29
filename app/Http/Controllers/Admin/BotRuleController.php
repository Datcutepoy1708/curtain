<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatBotRule;
use Illuminate\Http\Request;

class BotRuleController extends Controller
{
    public function index()
    {
        $rules = ChatBotRule::orderByDesc('priority')->orderByDesc('rule_id')->get();
        return view('admin.bot-rules.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_name'        => 'required|string|max:150',
            'keywords'         => 'required|string|max:1000',
            'match_type'       => 'required|in:CONTAINS,EXACT,REGEX',
            'response_message' => 'required|string',
            'quick_replies'    => 'nullable|string',
            'action_type'      => 'required|in:REPLY,HANDOVER_STAFF',
            'priority'         => 'nullable|integer',
        ]);

        $replies = null;
        if (!empty($validated['quick_replies'])) {
            $parts = array_filter(array_map('trim', explode(',', $validated['quick_replies'])));
            $replies = array_values($parts);
        }

        ChatBotRule::create([
            'rule_name'        => $validated['rule_name'],
            'keywords'         => $validated['keywords'],
            'match_type'       => $validated['match_type'],
            'response_message' => $validated['response_message'],
            'quick_replies'    => $replies,
            'action_type'      => $validated['action_type'],
            'priority'         => $validated['priority'] ?? 0,
            'is_active'        => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Thêm kịch bản Bot mới thành công!');
    }

    public function update(Request $request, $id)
    {
        $rule = ChatBotRule::findOrFail($id);

        $validated = $request->validate([
            'rule_name'        => 'required|string|max:150',
            'keywords'         => 'required|string|max:1000',
            'match_type'       => 'required|in:CONTAINS,EXACT,REGEX',
            'response_message' => 'required|string',
            'quick_replies'    => 'nullable|string',
            'action_type'      => 'required|in:REPLY,HANDOVER_STAFF',
            'priority'         => 'nullable|integer',
        ]);

        $replies = null;
        if (!empty($validated['quick_replies'])) {
            $parts = array_filter(array_map('trim', explode(',', $validated['quick_replies'])));
            $replies = array_values($parts);
        }

        $rule->update([
            'rule_name'        => $validated['rule_name'],
            'keywords'         => $validated['keywords'],
            'match_type'       => $validated['match_type'],
            'response_message' => $validated['response_message'],
            'quick_replies'    => $replies,
            'action_type'      => $validated['action_type'],
            'priority'         => $validated['priority'] ?? 0,
            'is_active'        => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Cập nhật kịch bản Bot thành công!');
    }

    public function toggle($id)
    {
        $rule = ChatBotRule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        $action = $rule->is_active ? 'kích hoạt' : 'tạm ngưng';
        return redirect()->back()->with('success', "Đã {$action} kịch bản '{$rule->rule_name}'.");
    }

    public function destroy($id)
    {
        $rule = ChatBotRule::findOrFail($id);
        $rule->delete();

        return redirect()->back()->with('success', 'Đã xóa kịch bản Bot thành công.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'rule_ids'   => 'required|array|min:1',
            'rule_ids.*' => 'integer|exists:chat_bot_rules,rule_id',
            'action'     => 'required|in:activate,deactivate,delete',
        ]);

        $ids = $validated['rule_ids'];
        $count = count($ids);

        if ($validated['action'] === 'activate') {
            ChatBotRule::whereIn('rule_id', $ids)->update(['is_active' => true]);
            $msg = "Đã kích hoạt {$count} kịch bản Bot thành công!";
        } elseif ($validated['action'] === 'deactivate') {
            ChatBotRule::whereIn('rule_id', $ids)->update(['is_active' => false]);
            $msg = "Đã tạm ngưng {$count} kịch bản Bot.";
        } else {
            ChatBotRule::whereIn('rule_id', $ids)->delete();
            $msg = "Đã xóa {$count} kịch bản Bot thành công.";
        }

        return redirect()->back()->with('success', $msg);
    }
}
