(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        const config = window.agendaConfig;
        if (!config) {
            console.error('agenda.js: window.agendaConfig is missing — the page script block did not run before this file.');
            return;
        }

        const dateInput = document.getElementById('agendaDateInput');
        const doctorSelect = document.getElementById('agendaDoctorFilter');
        const prevBtn = document.getElementById('agendaPrevDay');
        const nextBtn = document.getElementById('agendaNextDay');
        const todayBtn = document.getElementById('agendaToday');
        const content = document.getElementById('agendaContent');
        const banner = document.getElementById('agendaStatusBanner');

        if (!dateInput || !content) {
            console.error('agenda.js: expected page elements are missing.');
            return;
        }

        let currentDate = config.initialDate;
        let currentDoctorId = config.initialDoctorId || '';
        let inFlightController = null;

        function shiftDate(days) {
            const parts = currentDate.split('-').map(Number);
            const d = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));
            d.setUTCDate(d.getUTCDate() + days);
            const y = d.getUTCFullYear();
            const m = String(d.getUTCMonth() + 1).padStart(2, '0');
            const day = String(d.getUTCDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function todayString() {
            const d = new Date();
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function setBanner(message, type) {
            banner.textContent = '';
            if (!message) return;
            const div = document.createElement('div');
            div.className = 'alert alert-' + type + ' d-flex align-items-center gap-2 mb-0';

            const span = document.createElement('span');
            span.textContent = message;
            div.appendChild(span);

            if (type === 'danger') {
                const retryBtn = document.createElement('button');
                retryBtn.type = 'button';
                retryBtn.className = 'btn btn-sm btn-outline-danger ms-auto';
                retryBtn.textContent = config.labels.retry;
                retryBtn.addEventListener('click', load);
                div.appendChild(retryBtn);
            }

            banner.appendChild(div);
        }

        function badgeClass(status) {
            switch (String(status || '').toLowerCase()) {
                case 'completed': return 'text-bg-success';
                case 'cancelled': return 'text-bg-danger';
                case 'no_show':
                case 'no-show': return 'text-bg-secondary';
                case 'confirmed': return 'text-bg-primary';
                default: return 'text-bg-light border';
            }
        }

        function buildAppointmentRow(appointment) {
            const row = document.createElement('div');
            row.className = 'list-group-item d-flex flex-wrap align-items-center gap-3';

            const timeCol = document.createElement('div');
            timeCol.className = 'text-nowrap fw-semibold';
            timeCol.style.minWidth = '100px';
            let timeText = appointment.start_time || '';
            if (appointment.end_time) timeText += ' \u2013 ' + appointment.end_time;
            timeCol.textContent = timeText;
            row.appendChild(timeCol);

            const mainCol = document.createElement('div');
            mainCol.className = 'flex-grow-1';

            const nameDiv = document.createElement('div');
            nameDiv.textContent = appointment.patient_name || config.labels.unknownPatient;
            mainCol.appendChild(nameDiv);

            if (appointment.room_name) {
                const roomDiv = document.createElement('div');
                roomDiv.className = 'text-muted small';
                roomDiv.textContent = appointment.room_name;
                mainCol.appendChild(roomDiv);
            }

            if (appointment.notes) {
                const notesDiv = document.createElement('div');
                notesDiv.className = 'text-muted small';
                notesDiv.textContent = appointment.notes;
                mainCol.appendChild(notesDiv);
            }
            row.appendChild(mainCol);

            if (appointment.status) {
                const badge = document.createElement('span');
                badge.className = 'badge ' + badgeClass(appointment.status);
                badge.textContent = appointment.status;
                row.appendChild(badge);
            }

            return row;
        }

        function buildDoctorCard(doctor, appointments) {
            const card = document.createElement('div');
            card.className = 'card zedan-card shadow-sm mb-3';
            card.dataset.doctorCard = doctor.id;

            const header = document.createElement('div');
            header.className = 'card-header bg-transparent d-flex align-items-center gap-2';

            const icon = document.createElement('i');
            icon.className = 'bi bi-person-badge text-primary';
            header.appendChild(icon);

            const nameSpan = document.createElement('span');
            nameSpan.className = 'fw-semibold';
            nameSpan.textContent = doctor.name;
            header.appendChild(nameSpan);

            const countBadge = document.createElement('span');
            countBadge.className = 'badge text-bg-light border ms-auto';
            countBadge.textContent = String(appointments.length);
            header.appendChild(countBadge);

            card.appendChild(header);

            const body = document.createElement('div');
            body.className = 'card-body p-0';

            if (appointments.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'text-muted text-center py-4';
                empty.textContent = config.labels.noAppointmentsForDoctor;
                body.appendChild(empty);
            } else {
                const list = document.createElement('div');
                list.className = 'list-group list-group-flush';
                appointments
                    .slice()
                    .sort((a, b) => String(a.start_time || '').localeCompare(String(b.start_time || '')))
                    .forEach((appointment) => list.appendChild(buildAppointmentRow(appointment)));
                body.appendChild(list);
            }

            card.appendChild(body);
            return card;
        }

        function render(appointments) {
            content.textContent = '';

            if (!config.doctors || config.doctors.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'text-center text-muted py-5';
                empty.textContent = config.labels.noDoctors;
                content.appendChild(empty);
                return;
            }

            const byDoctor = {};
            const unassigned = [];
            appointments.forEach((appointment) => {
                if (appointment.doctor_id === null || appointment.doctor_id === undefined) {
                    unassigned.push(appointment);
                    return;
                }
                const key = String(appointment.doctor_id);
                (byDoctor[key] = byDoctor[key] || []).push(appointment);
            });

            config.doctors
                .filter((doctor) => !currentDoctorId || String(currentDoctorId) === String(doctor.id))
                .forEach((doctor) => {
                    content.appendChild(buildDoctorCard(doctor, byDoctor[String(doctor.id)] || []));
                });

            if (unassigned.length > 0 && !currentDoctorId) {
                content.appendChild(buildDoctorCard({ id: 'unassigned', name: config.labels.unassigned }, unassigned));
            }
        }

        function updateUrl() {
            const params = new URLSearchParams(window.location.search);
            params.set('date', currentDate);
            if (currentDoctorId) {
                params.set('doctor_id', currentDoctorId);
            } else {
                params.delete('doctor_id');
            }
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.replaceState({}, '', newUrl);
        }

        async function load() {
            if (inFlightController) {
                inFlightController.abort();
            }
            inFlightController = new AbortController();

            setBanner(config.labels.loading, 'secondary');
            dateInput.value = currentDate;
            if (doctorSelect) doctorSelect.value = currentDoctorId;
            updateUrl();

            const url = config.dataUrl + '?date=' + encodeURIComponent(currentDate)
                + (currentDoctorId ? '&doctor_id=' + encodeURIComponent(currentDoctorId) : '');

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    signal: inFlightController.signal,
                });

                let payload = null;
                try {
                    payload = await response.json();
                } catch {
                    // Non-JSON response (e.g. an HTML error page) — payload stays null,
                    // handled below by the !response.ok / !payload checks.
                }

                if (!response.ok || !payload) {
                    setBanner((payload && payload.message) || config.labels.loadFailed, 'danger');
                    return;
                }

                render(payload.appointments || []);
                setBanner('', null);
            } catch (error) {
                if (error.name === 'AbortError') return; // superseded by a newer request
                setBanner(config.labels.loadFailed, 'danger');
            }
        }

        prevBtn && prevBtn.addEventListener('click', () => {
            currentDate = shiftDate(-1);
            load();
        });

        nextBtn && nextBtn.addEventListener('click', () => {
            currentDate = shiftDate(1);
            load();
        });

        todayBtn && todayBtn.addEventListener('click', () => {
            currentDate = todayString();
            load();
        });

        dateInput.addEventListener('change', () => {
            if (dateInput.value) {
                currentDate = dateInput.value;
                load();
            }
        });

        doctorSelect && doctorSelect.addEventListener('change', () => {
            currentDoctorId = doctorSelect.value;
            load();
        });

        const filterForm = document.getElementById('agendaFilterForm');
        filterForm && filterForm.addEventListener('submit', (event) => {
            // Only reachable via <noscript>, but guard it anyway in case this script
            // ever fails to load (e.g. the asset build is stale) while the rest of
            // the page still renders — the plain GET fallback should still work.
            if (window.fetch) {
                event.preventDefault();
            }
        });
    }
})();
