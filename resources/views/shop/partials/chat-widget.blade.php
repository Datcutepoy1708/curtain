<!-- Floating Chat Launcher Button -->
<button type="button" class="chat-widget-launcher" id="chatWidgetToggle" title="Trò chuyện với CurtainLux">
    <i class="fa-solid fa-comments" id="chatLauncherIcon"></i>
    <span class="chat-online-badge"></span>
    <span class="chat-pulse-ring"></span>
</button>

<!-- Chat Widget Window -->
<div class="chat-widget-window" id="chatWidgetWindow">
    <!-- Header -->
    <div class="chat-widget-header">
        <div class="chat-header-profile">
            <div class="chat-header-avatar" id="chatHeaderAvatar">
                <i class="fa-solid fa-robot"></i>
                <span class="status-dot"></span>
            </div>
            <div>
                <h4 class="chat-header-name" id="chatHeaderTitle">CurtainBot — Tư Vấn Rèm</h4>
                <p class="chat-header-status" id="chatHeaderSubtitle">
                    <i class="fa-solid fa-circle" style="color: #22c55e; font-size: 8px;"></i>
                    <span>Trực tuyến • Sẵn sàng hỗ trợ</span>
                </p>
            </div>
        </div>

        <div class="chat-header-actions">
            <button type="button" class="btn-chat-handover" id="btnRequestStaff" title="Kết nối trực tiếp với nhân viên">
                <i class="fa-solid fa-headset"></i> Gặp nhân viên
            </button>
            <button type="button" class="btn-chat-action" id="btnChatMinimize" title="Thu nhỏ">
                <i class="fa-solid fa-minus"></i>
            </button>
        </div>
    </div>

    <!-- Message Thread -->
    <div class="chat-widget-messages" id="chatWidgetMessages">
        <!-- Messages loaded dynamically via chat-widget.js -->
    </div>

    <!-- Quick Reply Chips -->
    <div class="chat-quick-replies-wrap" id="chatQuickReplies">
        <!-- Quick chips rendered dynamically -->
    </div>

    <!-- Closed Notice & Start New Session (Phong cách java_angular) -->
    <div class="chat-closed-notice" id="chatClosedNotice" style="display: none; padding: 20px 16px; background: #faf8f5; border-top: 1px solid #e8e2d8; text-align: center;">
        <div style="font-size: 24px; color: #10b981; margin-bottom: 6px;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <p style="margin: 0 0 6px; font-size: 13.5px; font-weight: 700; color: #2d2621;">Phiên tư vấn này đã kết thúc</p>
        <p style="margin: 0 0 14px; font-size: 12px; color: #78716c;">Cảm ơn bạn đã liên hệ CurtainLux. Bạn có thể bấm nút bên dưới để mở phiên trò chuyện mới bất kỳ lúc nào.</p>
        <button type="button" class="btn-chat-restart" id="btnStartNewChat" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #2d2621 0%, #b8935c 100%); color: #fff; border: none; border-radius: 10px; padding: 10px 18px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(184, 147, 92, 0.35);">
            <i class="fa-solid fa-rotate-right"></i> Bắt Đầu Phiên Trò Chuyện Mới
        </button>
    </div>

    <!-- Footer Input Form -->
    <form class="chat-widget-footer" id="chatWidgetForm">
        <input type="text" 
               class="chat-input" 
               id="chatMessageInput" 
               placeholder="Nhập câu hỏi cần tư vấn rèm..." 
               autocomplete="off" 
               maxlength="1000">
        <button type="submit" class="btn-chat-send" id="btnChatSend" title="Gửi tin nhắn">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>
