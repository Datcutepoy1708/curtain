<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Consultation extends Model
{
    use HasFactory;

    // Service modes
    public const SERVICE_MODE_HOME_SURVEY = 'home_survey';
    public const SERVICE_MODE_REMOTE = 'remote_consultation';

    // Sample box statuses
    public const SAMPLE_BOX_NONE = 'none';
    public const SAMPLE_BOX_PENDING = 'pending';
    public const SAMPLE_BOX_SHIPPED = 'shipped';
    public const SAMPLE_BOX_DELIVERED = 'delivered';
    public const SAMPLE_BOX_CANCELLED = 'cancelled';

    // Measurement statuses
    public const MEASUREMENT_PENDING = 'pending';
    public const MEASUREMENT_GUIDED = 'guided_via_call';
    public const MEASUREMENT_CONFIRMED = 'confirmed';
    public const MEASUREMENT_SELF = 'customer_self_measured';

    // Installation methods
    public const INSTALL_SELF = 'self_install';
    public const INSTALL_CUSTOMER_ARRANGED = 'customer_arranged';
    public const INSTALL_NEED_ADVICE = 'need_installation_advice';

    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address',
        'city',
        'district',
        'ward',
        'province_code',
        'province_name',
        'service_mode',
        'contact_method',
        'sample_box_requested',
        'sample_box_status',
        'shipping_tracking_code',
        'measurement_status',
        'fit_guarantee_eligible',
        'fit_guarantee_confirmed_at',
        'fit_guarantee_confirmed_by',
        'installation_method',
        'preferred_date',
        'preferred_time',
        'curtain_types_interested',
        'estimated_windows',
        'notes',
        'status',
        'staff_id',
        'quotation_amount',
        'admin_note',
        'current_quotation_id',
        'estimated_amount',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'curtain_types_interested' => 'array',
        'estimated_windows' => 'integer',
        'quotation_amount' => 'decimal:0',
        'estimated_amount' => 'decimal:0',
        'sample_box_requested' => 'boolean',
        'fit_guarantee_eligible' => 'boolean',
        'fit_guarantee_confirmed_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->code)) {
                $item->code = 'LH-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function fitGuaranteeConfirmedBy()
    {
        return $this->belongsTo(User::class, 'fit_guarantee_confirmed_by');
    }

    public function windows()
    {
        return $this->hasMany(ConsultationWindow::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class)->orderBy('version', 'desc');
    }

    public function currentQuotation()
    {
        return $this->belongsTo(Quotation::class, 'current_quotation_id');
    }

    public function isRemote(): bool
    {
        return $this->service_mode === self::SERVICE_MODE_REMOTE;
    }

    public function isHomeSurvey(): bool
    {
        return $this->service_mode === self::SERVICE_MODE_HOME_SURVEY;
    }

    public static function isValidSampleBoxTransition(?string $current, string $next): bool
    {
        $current = $current ?: self::SAMPLE_BOX_NONE;
        if ($current === $next) return true;

        $validTransitions = [
            self::SAMPLE_BOX_NONE => [self::SAMPLE_BOX_PENDING],
            self::SAMPLE_BOX_PENDING => [self::SAMPLE_BOX_SHIPPED, self::SAMPLE_BOX_CANCELLED],
            self::SAMPLE_BOX_SHIPPED => [self::SAMPLE_BOX_DELIVERED, self::SAMPLE_BOX_CANCELLED],
            self::SAMPLE_BOX_DELIVERED => [],
            self::SAMPLE_BOX_CANCELLED => [],
        ];

        return in_array($next, $validTransitions[$current] ?? [], true);
    }

    public static function isValidMeasurementTransition(?string $current, string $next, bool $hasQuotations = false): bool
    {
        $current = $current ?: self::MEASUREMENT_PENDING;
        if ($current === $next) return true;

        // Khóa hoàn toàn mọi chuyển đổi số đo khi đã tồn tại bản báo giá chính thức
        if ($hasQuotations) {
            return false;
        }

        $validTransitions = [
            self::MEASUREMENT_PENDING => [self::MEASUREMENT_GUIDED, self::MEASUREMENT_CONFIRMED, self::MEASUREMENT_SELF],
            self::MEASUREMENT_GUIDED => [self::MEASUREMENT_CONFIRMED, self::MEASUREMENT_SELF],
            self::MEASUREMENT_CONFIRMED => [self::MEASUREMENT_GUIDED, self::MEASUREMENT_SELF],
            self::MEASUREMENT_SELF => [self::MEASUREMENT_GUIDED, self::MEASUREMENT_CONFIRMED],
        ];

        return in_array($next, $validTransitions[$current] ?? [], true);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => ['label' => 'Chờ tiếp nhận', 'color' => '#d97706', 'bg' => '#fef3c7'],
            'assigned' => ['label' => 'Đã phân công', 'color' => '#2563eb', 'bg' => '#dbeafe'],
            'surveying' => ['label' => 'Đang đo đạc', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'quoted' => ['label' => 'Đã lập báo giá', 'color' => '#0891b2', 'bg' => '#cffafe'],
            'completed' => ['label' => 'Đã ký hợp đồng / Tạo đơn', 'color' => '#16a34a', 'bg' => '#dcfce7'],
            'cancelled' => ['label' => 'Đã hủy', 'color' => '#dc2626', 'bg' => '#fee2e2'],
            default => ['label' => $this->status, 'color' => '#64748b', 'bg' => '#f1f5f9'],
        };
    }

    public function getServiceModeBadgeAttribute(): array
    {
        return match($this->service_mode) {
            self::SERVICE_MODE_REMOTE => [
                'label' => 'Tư Vấn Từ Xa',
                'color' => '#0284c7',
                'bg' => '#e0f2fe',
                'icon' => 'fa-video'
            ],
            default => [
                'label' => 'Khảo Sát Tại Nhà',
                'color' => '#15803d',
                'bg' => '#dcfce7',
                'icon' => 'fa-house-user'
            ],
        };
    }

    public function getSampleBoxBadgeAttribute(): array
    {
        return match($this->sample_box_status) {
            self::SAMPLE_BOX_PENDING => ['label' => 'Chờ gửi mẫu', 'color' => '#d97706', 'bg' => '#fef3c7'],
            self::SAMPLE_BOX_SHIPPED => ['label' => 'Đang vận chuyển', 'color' => '#2563eb', 'bg' => '#dbeafe'],
            self::SAMPLE_BOX_DELIVERED => ['label' => 'Đã nhận mẫu', 'color' => '#16a34a', 'bg' => '#dcfce7'],
            self::SAMPLE_BOX_CANCELLED => ['label' => 'Đã hủy gửi mẫu', 'color' => '#dc2626', 'bg' => '#fee2e2'],
            default => ['label' => 'Không yêu cầu', 'color' => '#64748b', 'bg' => '#f1f5f9'],
        };
    }

    public function getMeasurementBadgeAttribute(): array
    {
        return match($this->measurement_status) {
            self::MEASUREMENT_GUIDED => ['label' => 'Đang gọi video hướng dẫn', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            self::MEASUREMENT_CONFIRMED => ['label' => 'Đã chốt số đo (Bảo hiểm vừa vặn)', 'color' => '#16a34a', 'bg' => '#dcfce7'],
            self::MEASUREMENT_SELF => ['label' => 'Khách tự đo (Không bảo hiểm)', 'color' => '#ea580c', 'bg' => '#ffedd5'],
            default => ['label' => 'Chờ hướng dẫn đo', 'color' => '#64748b', 'bg' => '#f1f5f9'],
        };
    }
}
