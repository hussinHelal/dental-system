import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

window.bootstrap = bootstrap;

/**
 * Theme toggle (light/dark). Persisted on the user record via
 * /profile/theme so it follows the user across devices, per the
 * resolved assumption - not localStorage.
 */
function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
}

document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-theme-toggle]');
    if (!btn) return;

    const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    applyTheme(next);

    try {
        await fetch('/profile/theme', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({ theme: next }),
        });
    } catch (err) {
        console.error('Theme save failed', err);
    }
});

/**
 * Language switch (English/Arabic). Stored in session; a full reload
 * re-renders the whole layout in the new direction (RTL/LTR), fonts,
 * and translated strings.
 */
document.addEventListener('click', async (e) => {
    const link = e.target.closest('[data-locale-switch]');
    if (!link) return;
    e.preventDefault();

    const locale = link.getAttribute('data-locale-switch');

    try {
        await fetch('/locale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({ locale }),
        });
    } finally {
        window.location.reload();
    }
});

/**
 * Generic AJAX submit handler for every create/edit modal form. Forms
 * opt in with data-ajax-form. On 422 it renders errors inline next to
 * each field; on success it either follows a redirect URL or reloads
 * the current page so the underlying table refreshes.
 */
function clearFormErrors(form) {
    form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback[data-server-error]').forEach((el) => el.remove());
}

function showFormErrors(form, errors) {
    let matchedAny = false;

    Object.entries(errors).forEach(([field, messages]) => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) return;

        matchedAny = true;
        input.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback d-block';
        feedback.setAttribute('data-server-error', 'true');
        feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
        input.closest('.mb-3, .form-group, .col')?.appendChild(feedback);
    });

    return matchedAny;
}

document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!form.matches('[data-ajax-form]')) return;

    e.preventDefault();
    clearFormErrors(form);

    const submitBtn = form.querySelector('[type="submit"]');
    submitBtn?.setAttribute('disabled', 'disabled');

    try {
        const formData = new FormData(form);
        const method = (formData.get('_method') || form.method || 'POST').toString().toUpperCase();

        const response = await fetch(form.action, {
            method: method === 'GET' ? 'GET' : 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const data = await response.json().catch(() => ({}));

        if (response.status === 422 && data.errors) {
            const matched = showFormErrors(form, data.errors);
            if (!matched) {
                const firstError = Object.values(data.errors)[0];
                alert(Array.isArray(firstError) ? firstError[0] : firstError);
            }
            return;
        }

        if (!response.ok) {
            alert(data.message || window.i18n?.somethingWentWrong || 'Something went wrong. Please try again.');
            return;
        }

        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            window.location.reload();
        }
    } catch (err) {
        console.error('AJAX form submit failed', err);
        alert(window.i18n?.networkError || 'Network error. Please try again.');
    } finally {
        submitBtn?.removeAttribute('disabled');
    }
});

/**
 * Weekly revenue sparkline on the Doctor dashboard.
 */
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('weeklyRevenueChart');
    if (!canvas || !canvas.dataset.points) return;

    const points = JSON.parse(canvas.dataset.points);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: points.map((p) => p.label),
            datasets: [{
                label: 'Revenue',
                data: points.map((p) => p.amount),
                borderColor: '#2f6fed',
                backgroundColor: 'rgba(47, 111, 237, 0.15)',
                tension: 0.35,
                fill: true,
                pointRadius: 2,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
});
