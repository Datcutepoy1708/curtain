@php
    $provinces = config('provinces', []);
    $homeSurveyCodes = json_decode(\App\Models\Setting::get('home_survey_province_codes', '[]'), true) ?: [];
    $selectedProvince = old('province_code', '');
@endphp

<!-- Unified CurtainLux Survey & Remote Consultation Modal -->
<div id="surveyModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-calendar-check"></i> Đặt Lịch Khảo Sát & Tư Vấn Rèm</h3>
            <button type="button" class="btn-close-modal" onclick="closeSurveyModal()">&times;</button>
        </div>

        <form action="{{ route('consultation.store') }}" method="POST" class="modal-body" id="consultationBookingForm" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <!-- Validation Error Alert (Preserves Form State) -->
            @if($errors->consultation->any())
                <div style="background: #fef2f2; border: 1px solid #fecdd3; color: #b91c1c; padding: 12px 16px; border-radius: var(--radius-sm, 8px); font-size: 13px;">
                    <div style="font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-circle-exclamation"></i> Vui lòng kiểm tra lại các thông tin sau:
                    </div>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->consultation->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Dynamic Service Mode Info Banner -->
            <div id="serviceModeBanner" class="service-banner" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;">
                <i class="fa-solid fa-circle-info banner-icon" style="color: #64748b;"></i>
                <div class="banner-text">
                    Vui lòng chọn <strong>Tỉnh / Thành Phố</strong> bên dưới để CurtainLux tự động đề xuất phương thức phục vụ phù hợp nhất (Khảo sát tại nhà hoặc Tư vấn trực tuyến qua Video).
                </div>
            </div>

            <div class="form-group-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="modal-field">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Họ và Tên Của Bạn *</label>
                    <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="Ví dụ: Anh Hoàng / Chị Mai"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); font-size: 14px;">
                </div>
                <div class="modal-field">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Số Điện Thoại (Zalo) *</label>
                    <input type="tel" name="customer_phone" required value="{{ old('customer_phone') }}" placeholder="09xx xxx xxx"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); font-size: 14px;">
                </div>
            </div>

            <!-- Province Selector (34 Administrative Units) -->
            <div class="modal-field">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">
                    <i class="fa-solid fa-map-location-dot" style="color: var(--primary, #8C7A6B);"></i> Tỉnh / Thành Phố Nơi Lắp Rèm *
                </label>
                <select name="province_code" id="surveyProvinceSelect" required onchange="updateSurveyServiceMode()"
                        style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); background: #FFFFFF; font-size: 14px; font-weight: 600;">
                    <option value="" {{ empty($selectedProvince) ? 'selected' : '' }} disabled>-- Vui lòng chọn Tỉnh / Thành phố của bạn --</option>
                    @foreach($provinces as $pCode => $pName)
                        <option value="{{ $pCode }}" {{ $selectedProvince === (string)$pCode ? 'selected' : '' }}>
                            {{ $pName }} {{ in_array((string)$pCode, $homeSurveyCodes, true) ? '★ (Khảo sát tận nhà)' : '⚡ (Tư vấn từ xa 1-1 + Hộp mẫu)' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Detailed Address & Estimated Windows -->
            <div class="form-group-row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 14px;">
                <div class="modal-field">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Địa Chỉ Chi Tiết *</label>
                    <input type="text" name="address" required value="{{ old('address') }}" placeholder="Số nhà, tên đường, phường/xã, tòa nhà..."
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); font-size: 14px;">
                </div>
                <div class="modal-field">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Số Cửa Dự Kiến</label>
                    <input type="number" name="estimated_windows" value="{{ old('estimated_windows', 2) }}" min="1" max="50"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); font-size: 14px;">
                </div>
            </div>

            <!-- Date & Time (Unified Appointment) -->
            <div class="form-group-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="modal-field">
                    <label id="surveyDateLabel" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Ngày Hẹn Thuận Tiện *</label>
                    <input type="date" name="preferred_date" required min="{{ date('Y-m-d') }}" value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); font-size: 14px;">
                </div>
                <div class="modal-field">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Khung Giờ Thuận Tiện</label>
                    <select name="preferred_time" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); font-size: 14px;">
                        <option value="Sáng (08:30 - 11:30)" {{ old('preferred_time') === 'Sáng (08:30 - 11:30)' ? 'selected' : '' }}>Buổi Sáng (08:30 - 11:30)</option>
                        <option value="Chiều (14:00 - 17:30)" {{ old('preferred_time') === 'Chiều (14:00 - 17:30)' ? 'selected' : '' }}>Buổi Chiều (14:00 - 17:30)</option>
                        <option value="Tối (18:00 - 20:30)" {{ old('preferred_time') === 'Tối (18:00 - 20:30)' ? 'selected' : '' }}>Buổi Tối (18:00 - 20:30)</option>
                    </select>
                </div>
            </div>

            <!-- Remote Consultation Options (Shown ONLY for distant provinces) -->
            <div id="remoteOptionsWrapper" style="display: none; background: #FAF7F2; padding: 16px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9);">
                <div style="font-size: 14px; font-weight: 800; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-headset" style="color: var(--accent-cta, #B86B53);"></i> Tùy Chọn Tư Vấn Từ Xa Toàn Quốc
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Phương Thức Kết Nối *</label>
                        <select name="contact_method" id="contactMethodSelect" style="width: 100%; padding: 9px 10px; border-radius: 6px; border: 1px solid var(--border-line, #E8E2D9); font-size: 13px;">
                            <option value="zalo_video" {{ old('contact_method') === 'zalo_video' ? 'selected' : '' }}>Zalo Video Call 1-1 (Chuyên viên hướng dẫn đo trực quan)</option>
                            <option value="phone_call" {{ old('contact_method') === 'phone_call' ? 'selected' : '' }}>Gọi điện thoại thông thường</option>
                            <option value="google_meet" {{ old('contact_method') === 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Phương Án Thi Công Lắp Đặt *</label>
                        <select name="installation_method" id="installationMethodSelect" style="width: 100%; padding: 9px 10px; border-radius: 6px; border: 1px solid var(--border-line, #E8E2D9); font-size: 13px;">
                            <option value="self_install" {{ old('installation_method') === 'self_install' ? 'selected' : '' }}>Tự lắp đặt theo video hướng dẫn chi tiết của CurtainLux</option>
                            <option value="customer_arranged" {{ old('installation_method') === 'customer_arranged' ? 'selected' : '' }}>Thuê thợ cơ khí / mộc tại địa phương lắp ráp</option>
                            <option value="need_installation_advice" {{ old('installation_method') === 'need_installation_advice' ? 'selected' : '' }}>Cần CurtainLux tư vấn kết nối thợ trong khu vực</option>
                        </select>
                    </div>
                </div>

                <!-- Swatch Sample Box Checkbox -->
                <div style="margin-bottom: 12px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none;">
                        <input type="checkbox" name="sample_box_requested" value="1" id="sampleBoxCheckbox"
                               {{ old('sample_box_requested', '1') ? 'checked' : '' }}
                               style="width: 18px; height: 18px; accent-color: var(--accent-cta, #B86B53); cursor: pointer;">
                        <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                            Gửi Hộp Mẫu Vải Thực Tế Về Tận Nhà (Miễn phí ship bưu tá giao tận nơi)
                        </span>
                    </label>
                    <p style="font-size: 12px; color: var(--text-muted); margin: 4px 0 0 28px;">
                        CurtainLux sẽ gửi bưu phẩm chứa 15-20 mẫu vải dệt thực tế kèm catalogue phụ kiện để bạn trực tiếp sờ cảm nhận chất vải trước khi đặt may.
                    </p>
                </div>

                <!-- Fit Guarantee Card -->
                <div style="background: #FFFFFF; border-left: 3px solid #16a34a; padding: 10px 14px; border-radius: 6px; display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-shield-check" style="color: #16a34a; font-size: 16px; margin-top: 2px;"></i>
                    <div style="font-size: 12px; color: var(--text-main); line-height: 1.5;">
                        <strong style="color: #16a34a;">Cam Kết Bảo Hiểm Vừa Vặn 100%:</strong>
                        Khi tham gia buổi video call chốt số đo cùng kỹ thuật viên CurtainLux, nếu rèm thành phẩm bị sai lệch kích thước khi lắp đặt, chúng tôi cam kết <strong>sửa đổi hoặc may lại bộ rèm mới hoàn toàn miễn phí (01 lần/bộ rèm)</strong>.
                    </div>
                </div>
            </div>

            <!-- Notes (Product Context Preserved) -->
            <div class="modal-field">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main);">Ghi Chú Yêu Cầu Riêng (Nếu Có)</label>
                <textarea name="notes" id="surveyNotesField" rows="2" placeholder="Ví dụ: Mang thêm mẫu rèm vải tone be hoặc rèm cầu vồng cản sáng 100%..."
                          style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm, 8px); border: 1px solid var(--border-line, #E8E2D9); font-size: 14px;">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="surveySubmitBtn" class="btn-submit-survey"
                    style="width: 100%; padding: 14px; background: var(--accent-cta, #B86B53); color: #FFFFFF; border: none; border-radius: var(--radius-sm, 8px); font-size: 15px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s;">
                <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Khảo Sát
            </button>
        </form>
    </div>
</div>

<style>
.service-banner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-radius: var(--radius-sm, 8px);
    font-size: 13px;
    line-height: 1.5;
    transition: all 0.25s ease;
}
.service-banner.home-banner {
    background: #f0fdf4 !important;
    border: 1px solid #bbf7d0 !important;
    color: #166534 !important;
}
.service-banner.home-banner .banner-icon {
    color: #16a34a !important;
    font-size: 18px;
    margin-top: 2px;
}
.service-banner.remote-banner {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1e40af !important;
}
.service-banner.remote-banner .banner-icon {
    color: #2563eb !important;
    font-size: 18px;
    margin-top: 2px;
}
</style>

<script>
(function() {
    const HOME_SURVEY_CODES = {!! json_encode(array_values($homeSurveyCodes)) !!};

    window.updateSurveyServiceMode = function() {
        const provSelect = document.getElementById('surveyProvinceSelect');
        if (!provSelect) return;
        const selectedCode = String(provSelect.value || '').trim();

        const banner = document.getElementById('serviceModeBanner');
        const remoteWrapper = document.getElementById('remoteOptionsWrapper');
        const dateLabel = document.getElementById('surveyDateLabel');
        const submitBtn = document.getElementById('surveySubmitBtn');
        const contactMethod = document.getElementById('contactMethodSelect');
        const installMethod = document.getElementById('installationMethodSelect');

        if (!selectedCode) {
            if (banner) {
                banner.className = 'service-banner';
                banner.style.background = '#f8fafc';
                banner.style.border = '1px solid #e2e8f0';
                banner.style.color = '#475569';
                banner.innerHTML = '<i class="fa-solid fa-circle-info banner-icon" style="color: #64748b;"></i><div class="banner-text">Vui lòng chọn <strong>Tỉnh / Thành Phố</strong> nơi bạn sinh sống để CurtainLux tự động kích hoạt phương thức phục vụ phù hợp.</div>';
            }
            if (remoteWrapper) remoteWrapper.style.display = 'none';
            if (contactMethod) contactMethod.disabled = true;
            if (installMethod) installMethod.disabled = true;
            if (dateLabel) dateLabel.innerText = 'Ngày Hẹn Thuận Tiện *';
            if (submitBtn) submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Khảo Sát';
            return;
        }

        const isHome = HOME_SURVEY_CODES.includes(selectedCode);

        if (isHome) {
            if (banner) {
                banner.className = 'service-banner home-banner';
                banner.innerHTML = '<i class="fa-solid fa-house-chimney-user banner-icon"></i><div class="banner-text"><strong>Khu Vực Khảo Sát Tận Nhà:</strong> Chuyên viên CurtainLux mang đầy đủ catalogue mẫu vải dệt thực tế đến tận nhà, tư vấn phối màu hợp không gian và đo đạc chuẩn xác hoàn toàn miễn phí.</div>';
            }
            if (remoteWrapper) remoteWrapper.style.display = 'none';
            if (contactMethod) contactMethod.disabled = true;
            if (installMethod) installMethod.disabled = true;
            if (dateLabel) dateLabel.innerText = 'Ngày Hẹn Khảo Sát Tận Nhà *';
            if (submitBtn) submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Khảo Sát Tận Nhà';
        } else {
            if (banner) {
                banner.className = 'service-banner remote-banner';
                banner.innerHTML = '<i class="fa-solid fa-video banner-icon"></i><div class="banner-text"><strong>Tư Vấn Trực Tuyến Từ Xa:</strong> Hướng dẫn đo chuẩn xác qua Video Call 1-1, gửi hộp mẫu vải miễn phí tận nhà & cam kết <strong>Bảo hiểm vừa vặn 100%</strong>.</div>';
            }
            if (remoteWrapper) remoteWrapper.style.display = 'block';
            if (contactMethod) contactMethod.disabled = false;
            if (installMethod) installMethod.disabled = false;
            if (dateLabel) dateLabel.innerText = 'Ngày Hẹn Tư Vấn & Hướng Dẫn Đo Qua Call *';
            if (submitBtn) submitBtn.innerHTML = '<i class="fa-solid fa-video"></i> Đặt Lịch Tư Vấn & Nhận Hộp Mẫu';
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.updateSurveyServiceMode();
    });

    @if($errors->consultation->any())
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof openSurveyModal === 'function') {
            openSurveyModal();
        }
    });
    @endif
})();
</script>
