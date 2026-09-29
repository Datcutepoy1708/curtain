document.addEventListener('DOMContentLoaded', function () {
    // Accordion toggle
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const btn = item.querySelector('.faq-question-btn');
        if (btn) {
            btn.addEventListener('click', function () {
                const isOpen = item.classList.contains('is-open');
                // Close others if desired or allow multi-expand
                item.classList.toggle('is-open', !isOpen);
            });
        }
    });

    // Client-side quick search filter
    const searchInput = document.getElementById('faqSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            faqItems.forEach(item => {
                const q = (item.getAttribute('data-question') || '').toLowerCase();
                const a = (item.getAttribute('data-answer') || '').toLowerCase();
                if (!term || q.includes(term) || a.includes(term)) {
                    item.style.display = 'block';
                    if (term) {
                        item.classList.add('is-open');
                    }
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
