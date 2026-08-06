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

document.addEventListener('click', (e) => {
    const image = e.target.closest('img[data-image-preview]');
    if (!image) return;

    e.preventDefault();
    const modalEl = document.getElementById('imagePreviewModal');
    const modalImg = modalEl.querySelector('#imagePreviewModalImg');
    const title = modalEl.querySelector('#imagePreviewModalLabel');

    modalImg.src = image.src;
    modalImg.alt = image.alt || '';
    title.textContent = image.dataset.imageTitle || image.alt || '';

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
});

document.addEventListener('focusout', (e) => {
    const input = e.target.closest('input[data-time-autofill]');
    if (!input) return;

    const value = input.value.trim();
    if (value === '') {
        return;
    }

    const normalized = normalizeTimeValue(value);
    if (normalized) {
        input.value = normalized;
    }
});

document.addEventListener('change', (e) => {
    const modal = e.target.closest('#createAppointmentModal');
    if (!modal) return;

    const relevant = ['appointment_date', 'doctor_id', 'room_id'];
    if (!relevant.includes(e.target.name)) {
        return;
    }

    autoFillAppointmentStartTime(modal);
});

document.addEventListener('DOMContentLoaded', () => {
    const createModalElement = document.getElementById('createAppointmentModal');
    if (!createModalElement) return;

    createModalElement.addEventListener('shown.bs.modal', () => {
        autoFillAppointmentStartTime(createModalElement);
    });
});

function normalizeTimeValue(value) {
    const text = value.toLowerCase().replace(/\s+/g, '');

    if (/^\d{1,2}$/.test(text)) {
        const hour = parseInt(text, 10);
        if (hour === 0) {
            return '00:00';
        }
        if (hour >= 1 && hour <= 12) {
            return pad(hour) + ':00 PM';
        }
        if (hour >= 13 && hour <= 23) {
            return pad(hour) + ':00';
        }
        return null;
    }

    if (/^(\d{1,2}):(\d{2})$/.test(text)) {
        const parts = text.match(/^(\d{1,2}):(\d{2})$/);
        const hour = parseInt(parts[1], 10);
        const minute = parseInt(parts[2], 10);
        if (minute < 0 || minute > 59) {
            return null;
        }
        if (hour === 0) {
            return '12:' + pad(minute) + ' AM';
        }
        if (hour >= 1 && hour <= 11) {
            return pad(hour) + ':' + pad(minute) + ' PM';
        }
        if (hour === 12) {
            return pad(hour) + ':' + pad(minute) + ' PM';
        }
        if (hour >= 13 && hour <= 23) {
            return pad(hour - 12) + ':' + pad(minute) + ' PM';
        }
        return null;
    }

    if (/^(am|pm)(\d{1,2})$/.test(text)) {
        const parts = text.match(/^(am|pm)(\d{1,2})$/);
        const meridiem = parts[1];
        const hour = parseInt(parts[2], 10);
        if (hour >= 1 && hour <= 12) {
            return pad(hour) + ':00 ' + meridiem.toUpperCase();
        }
    }

    if (/^(am|pm)(\d{1,2}):(\d{2})$/.test(text)) {
        const parts = text.match(/^(am|pm)(\d{1,2}):(\d{2})$/);
        const meridiem = parts[1];
        const hour = parseInt(parts[2], 10);
        const minute = parts[3];
        if (hour >= 1 && hour <= 12 && minute >= '00' && minute <= '59') {
            return pad(hour) + ':' + minute + ' ' + meridiem.toUpperCase();
        }
    }

    if (/^(\d{1,2})(am|pm)$/.test(text)) {
        const parts = text.match(/^(\d{1,2})(am|pm)$/);
        const hour = parseInt(parts[1], 10);
        const meridiem = parts[2];
        if (hour >= 1 && hour <= 12) {
            return pad(hour) + ':00 ' + meridiem.toUpperCase();
        }
    }

    if (/^(\d{1,2}):(\d{2})(am|pm)$/.test(text)) {
        const parts = text.match(/^(\d{1,2}):(\d{2})(am|pm)$/);
        const hour = parseInt(parts[1], 10);
        const minute = parts[2];
        const meridiem = parts[3];
        if (hour >= 1 && hour <= 12 && minute >= '00' && minute <= '59') {
            return pad(hour) + ':' + minute + ' ' + meridiem.toUpperCase();
        }
    }

    return null;
}

function pad(number) {
    return number.toString().padStart(2, '0');
}

function parseTime24(time) {
    const [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
}

function formatTime12(minutes) {
    let hour = Math.floor(minutes / 60);
    const minute = minutes % 60;
    const meridiem = hour >= 12 ? 'PM' : 'AM';

    if (hour === 0) {
        hour = 12;
    } else if (hour > 12) {
        hour -= 12;
    }

    return `${hour}:${pad(minute)} ${meridiem}`;
}

function slotsOverlap(startA, endA, startB, endB) {
    return startA < endB && endA > startB;
}

function findNextAvailableStart(busyRanges) {
    const earliest = 13 * 60; // 1:00 PM
    const latest = 23 * 60; // 11:00 PM start for a 1h slot
    const duration = 60;

    const normalizedBusy = busyRanges
        .map(([start, end]) => [parseTime24(start), parseTime24(end)])
        .sort((a, b) => a[0] - b[0]);

    for (let start = earliest; start <= latest; start += 30) {
        const end = start + duration;
        const conflict = normalizedBusy.some(([busyStart, busyEnd]) => slotsOverlap(start, end, busyStart, busyEnd));
        if (!conflict) {
            return start;
        }
    }

    return null;
}

async function autoFillAppointmentStartTime(modal) {
    const availabilityUrl = modal.dataset.availabilityUrl;
    if (!availabilityUrl) {
        return;
    }

    const dateInput = modal.querySelector('input[name="appointment_date"]');
    const doctorInput = modal.querySelector('select[name="doctor_id"]');
    const roomInput = modal.querySelector('select[name="room_id"]');
    const startInput = modal.querySelector('input[name="start_time"]');
    const endInput = modal.querySelector('input[name="end_time"]');

    if (!dateInput || !startInput || !endInput) {
        return;
    }

    const date = dateInput.value;
    if (!date) {
        return;
    }

    const params = new URLSearchParams({ appointment_date: date });
    if (doctorInput?.value) {
        params.append('doctor_id', doctorInput.value);
    }
    if (roomInput?.value) {
        params.append('room_id', roomInput.value);
    }

    try {
        const response = await fetch(`${availabilityUrl}?${params.toString()}`, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        const busyRanges = (data.appointments || []).map((appointment) => [appointment.start_time, appointment.end_time]);
        const nextStart = findNextAvailableStart(busyRanges);

        if (nextStart !== null) {
            startInput.value = formatTime12(nextStart);
            if (!endInput.value.trim()) {
                endInput.value = formatTime12(nextStart + 60);
            }
        }
    } catch (err) {
        console.error('Failed to load appointment availability', err);
    }
}

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
    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        return;
    }

    clearFormErrors(form);

    const submitBtn = form.querySelector('[type="submit"]');
    submitBtn?.setAttribute('disabled', 'disabled');

    try {
        const formData = new FormData(form);
        const method = (formData.get('_method') || form.method || 'POST').toString().toUpperCase();
        // Debug info: log the target action and form keys
        console.log('AJAX submit:', form.action, method);
        for (const pair of formData.entries()) {
            console.log('  field:', pair[0], pair[1]);
        }

        const fetchOptions = {
            method,
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
        };

        // Do not attach a request body to GET/HEAD requests (fetch throws)
        if (method !== 'GET' && method !== 'HEAD') {
            fetchOptions.body = formData;
        }

        const response = await fetch(form.action, fetchOptions);

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
            const message = err?.message || window.i18n?.networkError || 'Network error. Please try again.';
            alert(`Network error: ${message}`);
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

// (removed stray duplicate success block)