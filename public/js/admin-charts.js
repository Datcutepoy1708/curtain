/**
 * CurtainLux Admin Charts Module
 * Interactive Chart.js Initializer for Dashboard and Statistics
 * 100% decoupled from Blade templates (zero inline scripts).
 */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        return;
    }

    // Chart.js Global Defaults for Japandi / ERP Theme
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';

    const formatVND = (value) => {
        return new Intl.NumberFormat('vi-VN').format(Math.round(value)) + ' ₫';
    };

    const formatCompactVND = (value) => {
        if (value >= 1000000) {
            return (value / 1000000).toFixed(1).replace('.0', '') + ' tr';
        }
        if (value >= 1000) {
            return (value / 1000).toFixed(0) + ' k';
        }
        return value;
    };

    // ── 1. Dashboard Revenue Spline Chart ───────────────────────────
    const dashCanvas = document.getElementById('dashboardRevenueChart');
    if (dashCanvas) {
        const rawLabels = dashCanvas.getAttribute('data-chart-labels');
        const rawValues = dashCanvas.getAttribute('data-chart-values');

        let labels = [];
        let values = [];

        try {
            labels = JSON.parse(rawLabels || '[]');
            values = JSON.parse(rawValues || '[]');
        } catch (e) {
            console.error('Failed to parse dashboard chart data:', e);
        }

        const ctx = dashCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.28)');
        gradient.addColorStop(0.6, 'rgba(37, 99, 235, 0.08)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

        new Chart(dashCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: values,
                    borderColor: '#2563eb',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.38,
                    pointRadius: values.length > 20 ? 1.5 : 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#f8fafc',
                        padding: 10,
                        cornerRadius: 8,
                        boxPadding: 4,
                        callbacks: {
                            label: function (context) {
                                return ' Doanh thu: ' + formatVND(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.12)',
                            drawBorder: false,
                        },
                        ticks: {
                            callback: formatCompactVND
                        }
                    }
                }
            }
        });
    }

    // ── 2. Statistics Revenue & Orders Dual Axis Chart ──────────────
    const statsCanvas = document.getElementById('statsRevenueTrendChart');
    if (statsCanvas) {
        let labels = [];
        let revenues = [];
        let orders = [];

        try {
            labels = JSON.parse(statsCanvas.getAttribute('data-chart-labels') || '[]');
            revenues = JSON.parse(statsCanvas.getAttribute('data-chart-revenue') || '[]');
            orders = JSON.parse(statsCanvas.getAttribute('data-chart-orders') || '[]');
        } catch (e) {
            console.error('Failed to parse stats trend chart data:', e);
        }

        const ctx = statsCanvas.getContext('2d');
        const revGrad = ctx.createLinearGradient(0, 0, 0, 280);
        revGrad.addColorStop(0, 'rgba(5, 150, 105, 0.32)');
        revGrad.addColorStop(1, 'rgba(5, 150, 105, 0.0)');

        new Chart(statsCanvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Số đơn rèm (đơn)',
                        data: orders,
                        borderColor: '#7c3aed',
                        backgroundColor: '#7c3aed',
                        borderWidth: 2,
                        pointRadius: 4,
                        yAxisID: 'yOrders',
                        tension: 0.3
                    },
                    {
                        type: 'bar',
                        label: 'Doanh thu (VNĐ)',
                        data: revenues,
                        backgroundColor: 'rgba(37, 99, 235, 0.8)',
                        hoverBackgroundColor: '#1d4ed8',
                        borderRadius: 6,
                        yAxisID: 'yRevenue'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                if (ctx.dataset.yAxisID === 'yRevenue') {
                                    return ' Doanh thu: ' + formatVND(ctx.parsed.y);
                                }
                                return ' Số đơn rèm: ' + ctx.parsed.y + ' đơn';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    yRevenue: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.12)'
                        },
                        ticks: {
                            callback: formatCompactVND
                        }
                    },
                    yOrders: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // ── 3. Statistics Category Breakdown Doughnut Chart ─────────────
    const catCanvas = document.getElementById('statsCategoryDoughnutChart');
    if (catCanvas) {
        let labels = [];
        let values = [];

        try {
            labels = JSON.parse(catCanvas.getAttribute('data-chart-labels') || '[]');
            values = JSON.parse(catCanvas.getAttribute('data-chart-values') || '[]');
        } catch (e) {
            console.error('Failed to parse category doughnut data:', e);
        }

        const colors = [
            '#2563eb', '#059669', '#d97706', '#7c3aed', '#0284c7', '#ea580c', '#ec4899', '#64748b'
        ];

        new Chart(catCanvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                return ' ' + ctx.label + ': ' + formatVND(ctx.parsed);
                            }
                        }
                    }
                }
            }
        });
    }

    // ── 4. Order Status Distribution Chart ──────────────────────────
    const statusCanvas = document.getElementById('statsOrderStatusChart');
    if (statusCanvas) {
        let labels = [];
        let values = [];

        try {
            labels = JSON.parse(statusCanvas.getAttribute('data-chart-labels') || '[]');
            values = JSON.parse(statusCanvas.getAttribute('data-chart-values') || '[]');
        } catch (e) {
            console.error('Failed to parse status chart data:', e);
        }

        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        '#d97706', // Chờ duyệt
                        '#2563eb', // Đã duyệt / Cắt may
                        '#7c3aed', // Đang gia công
                        '#0284c7', // Đang giao
                        '#16a34a', // Hoàn thành
                        '#dc2626'  // Đã hủy
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                return ' ' + ctx.label + ': ' + ctx.parsed + ' đơn';
                            }
                        }
                    }
                }
            }
        });
    }
});
