@extends('admin.layouts.app')

@section('title', 'Trung Tâm Trực Chat')
@section('page-title', 'Bàn Trực Tư Vấn Trực Tuyến & Phản Hồi Khách Hàng')

@section('content')
<div class="admin-chat-layout">
    <!-- Left Sidebar: Conversations List -->
    <div class="admin-chat-sidebar">
        <!-- Queue Header Tabs -->
        <div class="admin-chat-queue-tabs" style="display: flex; border-bottom: 1px solid var(--border); background: var(--surface); overflow-x: auto;">
            <a href="{{ route('admin.chats.index', ['tab' => 'waiting']) }}" 
               class="queue-tab-item {{ $tab === 'waiting' ? 'is-active' : '' }}" 
               style="padding: 10px 14px; font-size: 12.5px; font-weight: 700; text-decoration: none; color: {{ $tab === 'waiting' ? 'var(--brand)' : 'var(--on-surface-variant)' }}; border-bottom: 2px solid {{ $tab === 'waiting' ? 'var(--brand)' : 'transparent' }}; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                <i class="fa-solid fa-clock-rotate-left"></i> Chờ tiếp nhận
                @if($waitingCount > 0)
                    <span class="badge badge-danger" style="font-size: 10px; padding: 2px 6px; border-radius: 10px; animation: pulse 1.5s infinite;">{{ $waitingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.chats.index', ['tab' => 'my_chats']) }}" 
               class="queue-tab-item {{ $tab === 'my_chats' ? 'is-active' : '' }}" 
               style="padding: 10px 14px; font-size: 12.5px; font-weight: 700; text-decoration: none; color: {{ $tab === 'my_chats' ? 'var(--brand)' : 'var(--on-surface-variant)' }}; border-bottom: 2px solid {{ $tab === 'my_chats' ? 'var(--brand)' : 'transparent' }}; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                <i class="fa-solid fa-user-check"></i> Của tôi
                @if($myCount > 0)
                    <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px; border-radius: 10px;">{{ $myCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.chats.index', ['tab' => 'bot']) }}" 
               class="queue-tab-item {{ $tab === 'bot' ? 'is-active' : '' }}" 
               style="padding: 10px 14px; font-size: 12.5px; font-weight: 700; text-decoration: none; color: {{ $tab === 'bot' ? 'var(--brand)' : 'var(--on-surface-variant)' }}; border-bottom: 2px solid {{ $tab === 'bot' ? 'var(--brand)' : 'transparent' }}; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                <i class="fa-solid fa-robot"></i> Bot ({{ $botCount }})
            </a>

            <a href="{{ route('admin.chats.index', ['tab' => 'all']) }}" 
               class="queue-tab-item {{ $tab === 'all' ? 'is-active' : '' }}" 
               style="padding: 10px 14px; font-size: 12.5px; font-weight: 700; text-decoration: none; color: {{ $tab === 'all' ? 'var(--brand)' : 'var(--on-surface-variant)' }}; border-bottom: 2px solid {{ $tab === 'all' ? 'var(--brand)' : 'transparent' }}; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                Tất cả ({{ $allCount }})
            </a>
        </div>

        <div class="admin-chat-list">
            @forelse($conversations as $conv)
                @php
                    $isActive = ($selectedConversation && $selectedConversation->id === $conv->id);
                    $unread = $conv->unreadCount();
                @endphp
                <a href="{{ route('admin.chats.index', ['conversation_id' => $conv->id, 'tab' => $tab]) }}" 
                   class="admin-chat-item {{ $isActive ? 'is-active' : '' }}"
                   style="border-left: 4px solid {{ $conv->status === 'waiting_staff' ? '#f59e0b' : ($conv->status === 'staff_connected' ? '#10b981' : 'transparent') }};">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <strong style="font-size: 13.5px; color: var(--on-surface);">{{ $conv->customer_name }}</strong>
                        <span style="font-size: 10.5px; color: var(--on-surface-variant);">
                            {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 4px;">
                        <span style="font-size: 12px; color: var(--on-surface-variant); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 190px;">
                            {{ $conv->messages->last()?->message ?? 'Chưa có tin nhắn' }}
                        </span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            @if($unread > 0)
                                <span class="badge badge-danger" style="font-size: 10px; padding: 1px 6px;">{{ $unread }}</span>
                            @endif

                            @if($conv->status === 'waiting_staff')
                                <span class="badge badge-warning" style="font-size: 10px; padding: 2px 6px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">Chờ trực</span>
                            @elseif($conv->status === 'staff_connected')
                                <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">Đang trực</span>
                            @elseif($conv->status === 'closed')
                                <span class="badge badge-neutral" style="font-size: 10px; padding: 2px 6px;">Đóng</span>
                            @else
                                <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">Bot</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div style="padding: 40px 20px; text-align: center; color: var(--on-surface-variant); font-size: 13px;">
                    <i class="fa-regular fa-comment-dots" style="font-size: 28px; margin-bottom: 8px; opacity: 0.4;"></i>
                    <p style="margin: 0;">Không có cuộc hội thoại nào trong mục này.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right: Active Chat Conversation -->
    <div class="admin-chat-main" style="display: flex; flex-direction: column; height: 100%; min-height: 0; overflow: hidden; position: relative;">
        @if($selectedConversation)
            <!-- Chat Header -->
            <div style="padding: 12px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--surface); flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--brand); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                        {{ substr($selectedConversation->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <strong style="font-size: 14px; color: var(--on-surface); display: block;">{{ $selectedConversation->customer_name }}</strong>
                        <div style="font-size: 11.5px; color: var(--on-surface-variant); display: flex; align-items: center; gap: 8px;">
                            <span>Mã phiên: <code>#{{ $selectedConversation->id }}</code></span>
                            @if($selectedConversation->customer_phone)
                                <span>• SĐT: <strong>{{ $selectedConversation->customer_phone }}</strong></span>
                            @endif
                            @if($selectedConversation->staff)
                                <span>• Chuyên viên: <strong style="color: #047857;">{{ $selectedConversation->staff->name }}</strong></span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    @if($selectedConversation->status !== 'staff_connected' && $selectedConversation->status !== 'closed')
                        <form action="{{ route('admin.chats.takeover', $selectedConversation->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm" style="font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-headset"></i> Tiếp Nhận Cuộc Trò Chuyện
                            </button>
                        </form>
                    @endif

                    @if($selectedConversation->status !== 'closed')
                        <form action="{{ route('admin.chats.close', $selectedConversation->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc muốn đóng phiên trò chuyện này? Hệ thống sẽ gửi thông báo kết thúc cho khách.');">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #fca5a5;">
                                <i class="fa-solid fa-check"></i> Đóng Phiên
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Waiting Alert Banner if customer is in waiting_staff -->
            @if($selectedConversation->status === 'waiting_staff')
                <div style="background: #fef3c7; border-bottom: 1px solid #fde68a; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="color: #92400e; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-bell fa-bounce" style="color: #d97706; font-size: 15px;"></i>
                        <span><strong>Khách hàng đang chờ nhân viên tư vấn!</strong> Vui lòng bấm tiếp nhận để bắt đầu hỗ trợ.</span>
                    </div>
                    <form action="{{ route('admin.chats.takeover', $selectedConversation->id) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm" style="font-weight: 700; background: #d97706; color: #fff; border: none; padding: 6px 14px;">
                            <i class="fa-solid fa-headset"></i> Tiếp Nhận Ngay
                        </button>
                    </form>
                </div>
            @endif

            <input type="hidden" id="currentConversationId" value="{{ $selectedConversation->id }}">

            <!-- Messages Feed -->
            <div class="admin-chat-feed" id="adminChatMessages" style="flex: 1 1 0; min-height: 0; overflow-y: auto; padding: 14px 18px; background: var(--surface-alt); display: flex; flex-direction: column; gap: 8px;">
                @foreach($selectedConversation->messages as $msg)
                    <div class="admin-msg-row {{ $msg->sender_type }}" data-msg-id="{{ $msg->id }}">
                        <div class="admin-msg-bubble {{ $msg->sender_type }}">
                            @if($msg->sender_type === 'bot')
                                <div style="font-size: 10px; font-weight: 700; opacity: 0.8; margin-bottom: 2px;">
                                    <i class="fa-solid fa-robot"></i> CurtainBot
                                </div>
                            @elseif($msg->sender_type === 'staff')
                                <div style="font-size: 10px; font-weight: 700; opacity: 0.8; margin-bottom: 2px;">
                                    <i class="fa-solid fa-user-tie"></i> {{ $msg->sender->name ?? 'Chuyên viên tư vấn' }}
                                </div>
                            @endif
                            <div class="msg-text">{{ $msg->message }}</div>
                            <span class="admin-msg-time">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer Input Form -->
            @if($selectedConversation->status !== 'closed')
                <form id="adminReplyForm" style="flex-shrink: 0; padding: 14px 18px; border-top: 1px solid var(--border); display: flex; gap: 10px; background: var(--surface); z-index: 10;">
                    <input type="text" 
                           id="adminReplyInput" 
                           class="form-control" 
                           placeholder="Nhập tin nhắn phản hồi trực tiếp cho khách hàng..." 
                           autocomplete="off" 
                           style="flex: 1; padding: 10px 14px; font-size: 13.5px;"
                           required>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; flex-shrink: 0;">
                        <i class="fa-solid fa-paper-plane"></i> Gửi Tin
                    </button>
                </form>
            @else
                <div style="flex-shrink: 0; padding: 14px 20px; border-top: 1px solid var(--border); background: #f9fafb; text-align: center; color: var(--on-surface-variant); font-size: 13px;">
                    <i class="fa-solid fa-lock" style="margin-right: 6px;"></i> Cuộc trò chuyện này đã kết thúc. Khách hàng có thể bấm "Bắt đầu cuộc trò chuyện mới" trên website để mở phiên mới.
                </div>
            @endif
        @else
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; color: var(--on-surface-variant); padding: 40px;">
                <i class="fa-solid fa-comments" style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;"></i>
                <h3 style="margin: 0 0 6px; font-size: 16px; color: var(--on-surface);">Trung Tâm Trực Chat CurtainLux</h3>
                <p style="margin: 0; font-size: 13px;">Chọn một cuộc hội thoại từ danh sách bên trái để bắt đầu hỗ trợ khách hàng.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-chat.js') }}"></script>
@endpush
