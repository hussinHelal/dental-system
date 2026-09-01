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

function setupAjaxForms() {
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('[data-ajax-form]')) return;

        event.preventDefault();
        if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
            return;
        }

        clearFormErrors(form);

        const submitBtn = form.querySelector('[type="submit"]');
        submitBtn?.setAttribute('disabled', 'disabled');

        try {
            const formData = new FormData(form);
            const method = (formData.get('_method') || form.method || 'POST').toString().toUpperCase();

            const fetchOptions = {
                method,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
            };

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
                alert(data.message || 'Something went wrong. Please try again.');
                return;
            }

            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        } catch (error) {
            console.error('AJAX form submit failed', error);
            alert('Network error. Please try again.');
        } finally {
            submitBtn?.removeAttribute('disabled');
        }
    });
}

if (typeof window !== 'undefined') {
    setupAjaxForms();
}

export {};
