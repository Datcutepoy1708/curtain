<!-- Survey Booking Modal -->
<div class="modal-backdrop" id="surveyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-calendar-check"></i> Đặt Lịch Khảo Sát & Mang Mẫu Tận Nhà</h3>
            <button type="button" class="btn-close-modal" onclick="closeSurveyModal()">&times;</button>
        </div>
        <form action="{{ route('consultation.store') }}" method="POST" class="modal-body">
            @csrf
            <p class="modal-desc">Chuyên viên CurtainLux sẽ mang đầy đủ cây mẫu vải thực tế đến tận nơi, tư vấn phối màu hợp phong thủy và đo đạc kích thước chuẩn xác <strong>hoàn toàn miễn phí</strong>.</p>
            
            <div class="modal-field">
                <label>Họ và Tên Của Bạn *</label>
                <input type="text" name="customer_name" required placeholder="Ví dụ: Anh Hoàng / Chị Mai">
            </div>

            <div class="form-group-row">
                <div class="modal-field">
                    <label>Số Điện Thoại (Zalo) *</label>
                    <input type="tel" name="customer_phone" required placeholder="09xx xxx xxx">
                </div>
                <div class="modal-field">
                    <label>Số Lượng Ô Cửa Dự Kiến</label>
                    <input type="number" name="estimated_windows" value="2" min="1" max="50">
                </div>
            </div>

            <div class="modal-field">
                <label>Địa Chỉ Khảo Sát *</label>
                <input type="text" name="address" required placeholder="Số nhà, tên đường, tòa nhà chung cư...">
            </div>

            <div class="form-group-row">
                <div class="modal-field">
                    <label>Ngày Hẹn Mong Muốn *</label>
                    <input type="date" name="preferred_date" required value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div class="modal-field">
                    <label>Khung Giờ Thuận Tiện</label>
                    <select name="preferred_time">
                        <option value="Sáng (08:30 - 11:30)">Buổi Sáng (08:30 - 11:30)</option>
                        <option value="Chiều (14:00 - 17:30)">Buổi Chiều (14:00 - 17:30)</option>
                        <option value="Tối (18:00 - 20:30)">Buổi Tối (18:00 - 20:30)</option>
                    </select>
                </div>
            </div>

            <div class="modal-field">
                <label>Ghi Chú Yêu Cầu Riêng (Nếu Có)</label>
                <textarea name="notes" id="surveyNotesField" rows="2" placeholder="Ví dụ: Mang thêm mẫu rèm vải tone be hoặc rèm cầu vồng cản sáng 100%..."></textarea>
            </div>

            <button type="submit" class="btn-submit-survey">
                <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Khảo Sát Miễn Phí
            </button>
        </form>
    </div>
</div>
