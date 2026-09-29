/**
 * CurtainLux Error Page Script
 * Strict separation: Countdown redirect and navigational helpers
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Automatic Countdown for 404
    const countdownEl = document.getElementById('countdown');
    let timeLeft = 5;
    let timerId = null;

    if (countdownEl) {
        timerId = setInterval(() => {
            timeLeft -= 1;
            if (timeLeft <= 0) {
                clearInterval(timerId);
                countdownEl.textContent = '0';
                window.location.href = '/';
            } else {
                countdownEl.textContent = timeLeft;
            }
        }, 1000);
    }

    // 2. Go back button helper
    const backBtns = document.querySelectorAll('[data-action="go-back"]');
    backBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (timerId) clearInterval(timerId);
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        });
    });
});
