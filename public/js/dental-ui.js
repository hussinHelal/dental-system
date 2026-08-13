(() => {
    'use strict';

    const parseHumanAmount = (value) => {
        if (value === null || value === undefined) return null;

        let normalized = String(value)
            .trim()
            .toLowerCase()
            .replace(/\s+/g, '')
            .replace(/,/g, '');

        if (!normalized) return null;

        let multiplier = 1;
        const suffix = normalized.slice(-1);
        if (suffix === 'k' || suffix === 'm' || suffix === 'b') {
            multiplier = suffix === 'k' ? 1e3 : suffix === 'm' ? 1e6 : 1e9;
            normalized = normalized.slice(0, -1);
        }

        if (!/^\d+(\.\d+)?$/.test(normalized)) return null;

        const amount = Number(normalized) * multiplier;
        return Number.isFinite(amount) ? amount : null;
    };

    const trimZeros = (value) => {
        return Number(value.toFixed(2)).toString();
    };

    const compactMoney = (value) => {
        const amount = typeof value === 'number' ? value : parseHumanAmount(value);
        if (amount === null || !Number.isFinite(amount)) return '';

        const abs = Math.abs(amount);
        let divisor = 1;
        let suffix = '';

        if (abs >= 1e9) {
            divisor = 1e9;
            suffix = 'b';
        } else if (abs >= 1e6) {
            divisor = 1e6;
            suffix = 'm';
        } else if (abs >= 1e3) {
            divisor = 1e3;
            suffix = 'k';
        }

        if (!suffix) {
            return Number.isInteger(amount) ? String(amount) : trimZeros(amount);
        }

        const scaled = amount / divisor;
        return `${trimZeros(scaled)}${suffix}`;
    };

    const plainMoney = (value) => {
        const amount = parseHumanAmount(value);
        if (amount === null) return '';
        return trimZeros(amount);
    };

    const initMoneyInputs = (root = document) => {
        root.querySelectorAll('[data-money-input]').forEach((input) => {
            if (input.dataset.moneyInitialized === '1') return;
            input.dataset.moneyInitialized = '1';

            const initial = input.value;
            if (initial !== '') input.value = compactMoney(initial);

            input.addEventListener('blur', () => {
                if (input.value.trim() !== '') input.value = compactMoney(input.value);
            });

            input.closest('form')?.addEventListener('submit', () => {
                const parsed = plainMoney(input.value);
                if (parsed !== '') input.value = parsed;
            });
        });

        root.querySelectorAll('[data-compact-money]').forEach((element) => {
            if (element.dataset.moneyInitialized === '1') return;
            element.dataset.moneyInitialized = '1';
            const value = element.dataset.value ?? element.textContent;
            const formatted = compactMoney(value);
            if (formatted !== '') element.textContent = formatted;
        });
    };

    const normalizePatients = (payload) => {
        const source = Array.isArray(payload)
            ? payload
            : Array.isArray(payload?.data)
                ? payload.data
                : Array.isArray(payload?.patients)
                    ? payload.patients
                    : Array.isArray(payload?.results)
                        ? payload.results
                        : [];

        return source.map((patient) => ({
            id: patient.id ?? patient.value,
            name: patient.name ?? patient.full_name ?? patient.label ?? '',
            phone: patient.phone ?? patient.mobile ?? '',
        })).filter((patient) => patient.id && patient.name);
    };

    const initPatientAutocomplete = (root = document) => {
        root.querySelectorAll('[data-patient-autocomplete]').forEach((wrapper) => {
            if (wrapper.dataset.autocompleteInitialized === '1') return;
            wrapper.dataset.autocompleteInitialized = '1';

            const input = wrapper.querySelector('[data-patient-search-input]');
            const menu = wrapper.querySelector('[data-patient-results]');
            const hiddenId = wrapper.querySelector('[data-patient-id]');
            const targetName = wrapper.dataset.targetName ? document.querySelector(wrapper.dataset.targetName) : null;
            const targetPhone = wrapper.dataset.targetPhone ? document.querySelector(wrapper.dataset.targetPhone) : null;
            const endpoint = wrapper.dataset.endpoint;
            if (!input || !menu || !endpoint) return;

            let controller = null;
            let timer = null;
            let highlighted = -1;

            const closeMenu = () => {
                menu.classList.add('d-none');
                menu.innerHTML = '';
                highlighted = -1;
            };

            const render = (patients) => {
                menu.innerHTML = '';
                highlighted = -1;

                if (!patients.length) {
                    closeMenu();
                    return;
                }

                patients.slice(0, 8).forEach((patient, index) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'dropdown-item text-start py-2';
                    button.dataset.index = String(index);
                    button.innerHTML = `
                        <div class="fw-semibold text-body"></div>
                        <div class="small text-secondary"></div>
                    `;
                    button.children[0].textContent = patient.name;
                    button.children[1].textContent = patient.phone || '';

                    button.addEventListener('mousedown', (event) => event.preventDefault());
                    button.addEventListener('click', () => {
                        input.value = patient.name;
                        if (hiddenId) hiddenId.value = patient.id;
                        if (targetName) targetName.value = patient.name;
                        if (targetPhone && patient.phone) targetPhone.value = patient.phone;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                        closeMenu();
                    });
                    menu.appendChild(button);
                });

                menu.classList.remove('d-none');
            };

            const setHighlight = (index) => {
                const items = [...menu.querySelectorAll('[data-index]')];
                items.forEach((item) => item.classList.remove('active'));
                if (!items.length) return;
                highlighted = Math.max(0, Math.min(index, items.length - 1));
                items[highlighted].classList.add('active');
            };

            const search = async () => {
                const query = input.value.trim();
                if (query.length < 2) {
                    if (hiddenId) hiddenId.value = '';
                    closeMenu();
                    return;
                }

                if (controller) controller.abort();
                controller = new AbortController();

                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('q', query);

                try {
                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        signal: controller.signal,
                    });
                    if (!response.ok) throw new Error(`Patient lookup failed: ${response.status}`);
                    render(normalizePatients(await response.json()));
                } catch (error) {
                    if (error.name !== 'AbortError') closeMenu();
                }
            };

            input.addEventListener('input', () => {
                if (hiddenId) hiddenId.value = '';
                clearTimeout(timer);
                timer = setTimeout(search, 250);
            });

            input.addEventListener('keydown', (event) => {
                const items = [...menu.querySelectorAll('[data-index]')];
                if (event.key === 'ArrowDown' && items.length) {
                    event.preventDefault();
                    setHighlight(highlighted + 1);
                } else if (event.key === 'ArrowUp' && items.length) {
                    event.preventDefault();
                    setHighlight(highlighted - 1);
                } else if (event.key === 'Enter' && highlighted >= 0 && items[highlighted]) {
                    event.preventDefault();
                    items[highlighted].click();
                } else if (event.key === 'Escape') {
                    closeMenu();
                }
            });

            document.addEventListener('click', (event) => {
                if (!wrapper.contains(event.target)) closeMenu();
            });
        });
    };

    window.DentalUI = { compactMoney, parseHumanAmount, initMoneyInputs, initPatientAutocomplete };

    document.addEventListener('DOMContentLoaded', () => {
        initMoneyInputs();
        initPatientAutocomplete();
    });
})();
