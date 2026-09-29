/**
 * CurtainLux Storefront JavaScript
 * Handles real-time dimension calculation, option surcharges, and survey modal popups.
 */

// 1. Survey Modal Functions
function openSurveyModal() {
    const modal = document.getElementById('surveyModal');
    if (modal) modal.classList.add('active');
}

function closeSurveyModal() {
    const modal = document.getElementById('surveyModal');
    if (modal) modal.classList.remove('active');
}

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('surveyModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === this) closeSurveyModal();
        });
    }

    // 2. Real-time Dimension Calculator
    const inputWidth = document.getElementById('inputWidth');
    const inputHeight = document.getElementById('inputHeight');
    const basePriceEl = document.getElementById('basePrice');
    const priceUnitEl = document.getElementById('priceUnit');
    const minAreaEl = document.getElementById('minArea');

    if (inputWidth && inputHeight && basePriceEl && priceUnitEl) {
        const basePrice = parseFloat(basePriceEl.value);
        const priceUnit = priceUnitEl.value;
        const minArea = parseFloat(minAreaEl ? minAreaEl.value : 1.0);

        const lblCalculatedUnits = document.getElementById('lblCalculatedUnits');
        const lblBaseCost = document.getElementById('lblBaseCost');
        const lblOptionsCost = document.getElementById('lblOptionsCost');
        const lblGrandTotal = document.getElementById('lblGrandTotal');

        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' ₫';
        }

        function calculateCurtainPrice() {
            const widthCm = parseFloat(inputWidth.value) || 0;
            const heightCm = parseFloat(inputHeight.value) || 0;
            const widthMeters = widthCm / 100.0;
            const heightMeters = heightCm / 100.0;
            const area = widthMeters * heightMeters;

            let units = 1.0;
            let unitText = '';

            if (priceUnit === 'sqm') {
                units = Math.max(area, minArea);
                unitText = units.toFixed(2) + ' m²' + (area < minArea ? ' (tối thiểu ' + minArea + 'm²)' : '');
            } else if (priceUnit === 'meter') {
                units = Math.max(widthMeters, 1.0);
                unitText = units.toFixed(2) + ' mét ngang';
            } else {
                units = 1.0;
                unitText = '1 bộ';
            }

            if (lblCalculatedUnits) lblCalculatedUnits.textContent = unitText;

            const baseCost = units * basePrice;
            if (lblBaseCost) lblBaseCost.textContent = formatVND(baseCost);

            // Calculate options
            let optionsCost = 0;
            const selectedRadios = document.querySelectorAll('input[type="radio"]:checked');
            selectedRadios.forEach(radio => {
                const extra = parseFloat(radio.dataset.extra || 0);
                const impact = radio.dataset.impact;

                if (extra > 0) {
                    if (impact === 'per_meter') {
                        optionsCost += extra * widthMeters;
                    } else if (impact === 'per_sqm') {
                        optionsCost += extra * units;
                    } else {
                        optionsCost += extra; // fixed
                    }
                }
            });

            if (lblOptionsCost) lblOptionsCost.textContent = formatVND(optionsCost);

            const grandTotal = baseCost + optionsCost;
            if (lblGrandTotal) lblGrandTotal.textContent = formatVND(grandTotal);
        }

        inputWidth.addEventListener('input', calculateCurtainPrice);
        inputHeight.addEventListener('input', calculateCurtainPrice);
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', calculateCurtainPrice);
        });

        // Run initially
        calculateCurtainPrice();
    }

    // 3. Product Detail Multi-Image Gallery Switcher
    const mainImg = document.getElementById('mainProductImage');
    const thumbBtns = document.querySelectorAll('.detail-thumb-btn');

    if (mainImg && thumbBtns.length > 0) {
        mainImg.style.transition = 'opacity 0.2s ease';
        thumbBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const targetSrc = this.getAttribute('data-target-src');
                if (targetSrc) {
                    mainImg.style.opacity = '0.6';
                    mainImg.src = targetSrc;
                    setTimeout(() => {
                        mainImg.style.opacity = '1';
                    }, 120);

                    thumbBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }

    // 4. Wishlist AJAX Toggle & Toast Notifications
    function showToast(message, type = 'success') {
        let toastContainer = document.getElementById('curtainlux-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'curtainlux-toast-container';
            toastContainer.style.position = 'fixed';
            toastContainer.style.bottom = '24px';
            toastContainer.style.left = '24px';
            toastContainer.style.zIndex = '9999';
            toastContainer.style.display = 'flex';
            toastContainer.style.flexDirection = 'column';
            toastContainer.style.gap = '10px';
            toastContainer.style.pointerEvents = 'none';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `toast-popup toast-${type}`;
        toast.style.pointerEvents = 'auto';
        toast.style.background = type === 'success' ? '#1e293b' : '#334155';
        toast.style.color = '#ffffff';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '12px';
        toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.fontSize = '14px';
        toast.style.fontWeight = '600';
        toast.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';

        const iconHtml = type === 'success' 
            ? '<i class="fa-solid fa-circle-check" style="color: #22c55e; font-size: 16px;"></i>'
            : '<i class="fa-solid fa-circle-info" style="color: #38bdf8; font-size: 16px;"></i>';

        toast.innerHTML = `${iconHtml}<span>${message}</span>`;
        toastContainer.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.transform = 'translateY(20px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }

    // Check for existing flash toasts
    document.querySelectorAll('[data-toast-flash]').forEach(el => {
        const type = el.getAttribute('data-toast-flash') || 'info';
        const msg = el.getAttribute('data-toast-message') || '';
        if (msg) showToast(msg, type);
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.btn-wishlist-toggle');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const productId = btn.getAttribute('data-wishlist-id');
        if (!productId) return;

        btn.style.pointerEvents = 'none';

        try {
            const res = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await res.json();

            if (res.ok) {
                // Update all buttons for this product ID on the page
                const allButtons = document.querySelectorAll(`.btn-wishlist-toggle[data-wishlist-id="${productId}"]`);
                allButtons.forEach(b => {
                    const icon = b.querySelector('i');
                    if (data.is_wishlisted) {
                        b.classList.add('is-active');
                        b.setAttribute('title', 'Bỏ khỏi yêu thích');
                        if (icon) {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid');
                        }
                    } else {
                        b.classList.remove('is-active');
                        b.setAttribute('title', 'Thêm vào yêu thích');
                        if (icon) {
                            icon.classList.remove('fa-solid');
                            icon.classList.add('fa-regular');
                        }
                    }
                });

                // Update header badge
                const badge = document.getElementById('wishlistHeaderBadge');
                if (badge) {
                    badge.textContent = data.count;
                    if (data.count > 0) {
                        badge.style.display = 'inline-flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                // If on wishlist page and removed, remove card smoothly
                if (!data.is_wishlisted) {
                    const card = btn.closest('.product-card');
                    if (card && window.location.pathname.includes('/wishlist')) {
                        card.style.transition = 'all 0.35s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            // If no cards left, reload to show empty state
                            if (document.querySelectorAll('.product-card').length === 0) {
                                window.location.reload();
                            }
                        }, 350);
                    }
                }

                showToast(data.message, data.is_wishlisted ? 'success' : 'info');
            } else {
                showToast('Có lỗi xảy ra, vui lòng thử lại.', 'info');
            }
        } catch (err) {
            console.error('Wishlist toggle error:', err);
            showToast('Không thể kết nối máy chủ.', 'info');
        } finally {
            btn.style.pointerEvents = 'auto';
        }
    });

    // 4. Interactive Star Rating Selector
    const starRatingGroup = document.getElementById('starRatingGroup');
    const reviewRatingInput = document.getElementById('reviewRatingInput');
    const ratingDesc = document.getElementById('ratingDesc');

    if (starRatingGroup && reviewRatingInput) {
        const starButtons = starRatingGroup.querySelectorAll('.star-btn');
        const starDescriptions = {
            1: 'Rất không hài lòng (1 sao)',
            2: 'Chưa ưng ý (2 sao)',
            3: 'Bình thường / Tạm được (3 sao)',
            4: 'Hài lòng - Chất lượng tốt (4 sao)',
            5: 'Tuyệt vời - Rất ưng ý (5 sao)'
        };

        function setStarDisplay(score) {
            starButtons.forEach(btn => {
                const r = parseInt(btn.dataset.rating, 10);
                if (r <= score) {
                    btn.classList.add('active');
                    btn.classList.remove('fa-regular');
                    btn.classList.add('fa-solid');
                } else {
                    btn.classList.remove('active');
                    btn.classList.remove('fa-solid');
                    btn.classList.add('fa-regular');
                }
            });
            if (ratingDesc && starDescriptions[score]) {
                ratingDesc.textContent = starDescriptions[score];
            }
        }

        starButtons.forEach(btn => {
            btn.addEventListener('mouseenter', function () {
                const hoverScore = parseInt(this.dataset.rating, 10);
                setStarDisplay(hoverScore);
            });

            btn.addEventListener('click', function () {
                const clickedScore = parseInt(this.dataset.rating, 10);
                reviewRatingInput.value = clickedScore;
                setStarDisplay(clickedScore);
            });
        });

        starRatingGroup.addEventListener('mouseleave', function () {
            const currentScore = parseInt(reviewRatingInput.value, 10) || 5;
            setStarDisplay(currentScore);
        });
    }

    // 6. Interactive Product Gallery Slider & Thumbnails Strip
    const mainProductImage = document.getElementById('mainProductImage');
    const galleryThumbs = document.getElementById('galleryThumbs');
    const btnPrevSlide = document.getElementById('btnPrevSlide');
    const btnNextSlide = document.getElementById('btnNextSlide');
    const slideCounter = document.getElementById('slideCounter');

    if (mainProductImage) {
        const thumbButtons = galleryThumbs ? Array.from(galleryThumbs.querySelectorAll('.detail-thumb-btn')) : [];
        const totalSlides = thumbButtons.length > 0 ? thumbButtons.length : 1;
        let currentSlideIndex = 0;

        function updateSlide(newIndex) {
            if (newIndex < 0) newIndex = totalSlides - 1;
            if (newIndex >= totalSlides) newIndex = 0;
            currentSlideIndex = newIndex;

            if (thumbButtons[currentSlideIndex]) {
                const targetSrc = thumbButtons[currentSlideIndex].getAttribute('data-target-src');
                if (targetSrc) {
                    mainProductImage.classList.add('fade-out');
                    setTimeout(() => {
                        mainProductImage.src = targetSrc;
                        mainProductImage.classList.remove('fade-out');
                    }, 120);
                }

                // Update active thumbnail
                thumbButtons.forEach((btn, idx) => {
                    if (idx === currentSlideIndex) {
                        btn.classList.add('active');
                        btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }

            // Update counter badge
            if (slideCounter) {
                slideCounter.textContent = `${currentSlideIndex + 1} / ${totalSlides}`;
            }
        }

        if (btnPrevSlide) {
            btnPrevSlide.addEventListener('click', function (e) {
                e.preventDefault();
                updateSlide(currentSlideIndex - 1);
            });
        }

        if (btnNextSlide) {
            btnNextSlide.addEventListener('click', function (e) {
                e.preventDefault();
                updateSlide(currentSlideIndex + 1);
            });
        }

        thumbButtons.forEach((btn, idx) => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                updateSlide(idx);
            });
        });

        // Keyboard Arrow Navigation
        document.addEventListener('keydown', function (e) {
            if (totalSlides <= 1) return;
            // Only trigger if not focusing an input or textarea
            const tag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
            if (tag === 'input' || tag === 'textarea' || tag === 'select') return;

            if (e.key === 'ArrowLeft') {
                updateSlide(currentSlideIndex - 1);
            } else if (e.key === 'ArrowRight') {
                updateSlide(currentSlideIndex + 1);
            }
        });
    }
});


