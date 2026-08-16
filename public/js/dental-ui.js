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

    // Adds one row to a purchase's item-lines table (see purchases/partials/fields.blade.php).
    // Built via DOM methods rather than innerHTML + string concatenation on purpose:
    // item_name is free-text user input, and a name containing a quote or "<" would
    // otherwise break out of the HTML attribute and inject markup that runs for anyone
    // who later opens that purchase's edit modal.
    const addPurchaseItemRow = (tableId, itemName, quantity, unitPrice) => {
        itemName = itemName || '';
        quantity = quantity || '';
        unitPrice = unitPrice || '';

        const tbody = document.querySelector('#' + tableId + ' tbody');
        if (!tbody) {
            console.error('addPurchaseItemRow: no table with id "' + tableId + '"');
            return;
        }
        const index = tbody.children.length;

        const makeInput = (name, value, type, extra) => {
            const input = document.createElement('input');
            input.type = type || 'text';
            input.name = name;
            input.className = 'form-control form-control-sm';
            input.required = true;
            input.value = value; // DOM property assignment — always safe, no HTML parsing involved
            if (extra) {
                Object.keys(extra).forEach((key) => input.setAttribute(key, extra[key]));
            }
            return input;
        };

        const makeCell = (input) => {
            const td = document.createElement('td');
            td.appendChild(input);
            return td;
        };

        const row = document.createElement('tr');
        row.appendChild(makeCell(makeInput(`items[${index}][item_name]`, itemName)));
        row.appendChild(makeCell(makeInput(`items[${index}][quantity]`, quantity, 'number', { step: '0.01', min: '0.01' })));
        row.appendChild(makeCell(makeInput(`items[${index}][unit_price]`, unitPrice, 'number', { step: '0.01', min: '0' })));

        const removeCell = document.createElement('td');
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-outline-danger';
        removeBtn.textContent = '\u00d7';
        removeBtn.addEventListener('click', () => row.remove());
        removeCell.appendChild(removeBtn);
        row.appendChild(removeCell);

        tbody.appendChild(row);
    };

    // Exposed globally: purchases/partials/fields.blade.php calls this directly via
    // an onclick="" attribute (a plain global function, not window.DentalUI.___),
    // and purchases/index.blade.php calls it from inline <script> blocks to
    // pre-populate the edit modal with each purchase's existing items.
    window.addPurchaseItemRow = addPurchaseItemRow;

    window.DentalUI = { compactMoney, parseHumanAmount, initMoneyInputs, initPatientAutocomplete };

    document.addEventListener('DOMContentLoaded', () => {
        initMoneyInputs();
        initPatientAutocomplete();
    });
})();
