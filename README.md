# Zedan — Dental Clinic Management System

A local, single-clinic dental practice management system built on Laravel 12:
doctors, rooms, patients, appointments (with double-booking prevention),
a treatment catalog (including multi-session courses like Root Canal), cash
payments (paid now / pay later / installments), inventory with low-stock
flags, staff accounts, and PDF/Excel data backups — bilingual English/Arabic
with full RTL layout switching and a light/dark theme that follows the user.

## Tech stack

- **Framework:** Laravel 12, PHP 8.3+
- **Auth:** Laravel Sanctum installed (`HasApiTokens` on `User`) alongside
  classic session-based web auth (`Auth::attempt`) — Sanctum's SPA/token
  auth isn't exercised by the Blade UI itself, but the package is wired in
  so an API/mobile client could authenticate against the same app later.
- **Frontend:** Blade layouts + reusable Blade components
  (`x-modal`, `x-form-input`, `x-form-select`, `x-form-textarea`,
  `x-search-bar`, `x-empty-state`) — no SPA framework.
- **Styling:** Bootstrap 5.3 (prebuilt CSS, both LTR and RTL builds) plus a
  custom theme layer in `resources/css/theme.css` (brand colors, radius,
  shadows). Dark mode uses Bootstrap 5.3's built-in `data-bs-theme`
  color-mode system.
- **Roles:** `spatie/laravel-permission` for role storage (`Doctor`,
  `Receptionist`), enforced through Laravel Policies (`app/Policies`) at the
  controller layer, plus a `role:Doctor` route-middleware group for
  Doctor-only routes as defense-in-depth. The client-side Blade `@can`
  directives only hide/disable buttons — they are not the real boundary.
- **Database:** SQLite (`database/database.sqlite`) for this local,
  single-clinic install — zero server setup, and the whole clinic's data
  lives in one portable file that's trivial to back up alongside the
  PDF/Excel exports. Every migration avoids SQLite-only syntax, so the same
  schema can be pointed at MySQL later (see `config/database.php`, which
  keeps a ready-to-use `mysql` connection) for a multi-clinic/server rollout
  by changing four `.env` lines.
- **Images:** local `public` disk (symlinked), validated to images ≤2MB.
- **Exports:** `barryvdh/laravel-dompdf` (PDF) and `maatwebsite/excel`
  (Excel), one sheet/section per module, cover page + table of contents +
  summary counts.
- **Charts:** Chart.js (via npm/Vite) for the dashboard's weekly revenue
  sparkline, Doctor view only.

## Local setup

First-time setup:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
```

From then on, day-to-day development:

```bash
composer run dev
```

This runs the local server, a queue listener, and the Vite dev server
(with hot reload) together in one terminal, color-coded per process. It's
the standard script every fresh Laravel install ships with — the one
deliberate difference here is that `php artisan pail` (real-time log
tailing) is **not** included, since Pail depends on the `pcntl` PHP
extension, which doesn't exist on Windows. If you're on Mac/Linux and want
log tailing too, either run `php artisan pail` yourself in a second
terminal, or add it back into the `dev` script in `composer.json`:

```json
"dev": [
    "Composer\\Config::disableProcessTimeout",
    "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others"
]
```

`composer run dev` needs Node's `npx` on your `PATH` (comes with any
Node.js install). If you'd rather run things separately (e.g. inside a
NativePHP-managed process), `php artisan serve` on its own works exactly
as before.

Either way, then visit `http://localhost:8000`.

### Default accounts (`composer install` / local dev)

Running `php artisan migrate --seed` with the default `DatabaseSeeder` (full
demo data, for local dev/evaluation) creates:

| Role | Username | Password |
|---|---|---|
| Doctor (admin) | `doctor` | `password` |
| Receptionist | `reception` | `password` |

**Change both passwords after first login** (Profile page) — these are demo
credentials only.

### Packaged desktop builds (e.g. NativePHP)

A packaged build ships with an **empty database** — NativePHP clears it
before finalizing the package, and an end user has no terminal to run
`artisan migrate --seed` themselves. `AppServiceProvider` handles this: on
boot, it checks (once, cheaply) whether any user exists, and if not, runs
migrations and a **separate, minimal** `ProductionSeeder` — just the two
roles and one admin account, none of `DatabaseSeeder`'s demo patients/
doctors/appointments.

Set the admin account before packaging so every install isn't the same
password (`config/clinic.php`, backed by `.env`):

```
ADMIN_NAME="Your Clinic Name"
ADMIN_USERNAME=admin
ADMIN_PASSWORD=some-strong-password
```

Leave `ADMIN_PASSWORD` blank and a random one is generated and logged once
to `storage/logs/laravel.log` on first launch. From that one admin login,
every other account is created through the app's own Staff page — no CLI
needed on the end user's machine, ever.

(These are read via `config('clinic.*')`, not `env()` directly in the
seeder — a packaged build is likely to run `artisan config:cache`, after
which raw `env()` calls outside config files return null.)

### Automatic monthly backups

The scheduler needs the local machine's cron to trigger it. Add this to
your crontab (`crontab -e`):

```
* * * * * cd /path/to/zedan && php artisan schedule:run >> /dev/null 2>&1
```

This runs the `backup:generate --type=both` command on the 1st of every
month at 02:00, saves it to `storage/app/backups/YYYY-MM/`, and prunes
anything older than 12 months. You can also trigger a backup manually any
time from the Backups page (Doctor only) — Receptionist accounts can view
and download backup history but not create or delete one. Backups
generate in the background (see "Heavy-load hardening pass" below) — the
page shows Generating…/Ready/Failed per entry.

**Backup formats:** PDF and Excel are human-readable reports — good for
records, not for restoring data. **Database** is a raw copy of the live
SQLite file — the only format that can actually restore everything.
Generate a Database backup periodically if you want real disaster
recovery, not just PDF/Excel reports.

**Restoring** is deliberately a command-line operation, not a one-click
web button — swapping out a live database file from underneath a running
app risks a file-lock error or corruption (especially on Windows), and
that's not something to guess at without being able to test it. From the
Backups page, each completed Database backup has a "Restore instructions"
button showing the exact command:

```bash
# with the web server and queue worker both stopped:
php artisan backup:restore {backup_id}
```

This automatically makes a safety copy of the *current* database before
overwriting anything, so a mistaken restore is itself recoverable.

### Activity log

Every create/update/delete across doctors, rooms, patients, treatments,
appointments, payments, and inventory is logged automatically (who,
when, what changed) via `spatie/laravel-activitylog`, along with login/
logout/failed-login events. View it from the Activity Log page
(Doctor-only — sensitive audit data), filterable by staff member, module,
action, and date range, with an old-value/new-value diff per entry.
Passwords and remember-tokens are never logged, even on the `users`
table. Entries older than `config('activitylog.delete_records_older_than_days')`
(default 2 years) are pruned weekly by the scheduler.

## Production deployment

None of the scalability work in this codebase helps if the app is still
being run with `php artisan serve` — that command is explicitly a
single-threaded development server, not meant for production traffic at
all. For a real, busy clinic:

**Use a real web server + PHP-FPM**, not `artisan serve`. Nginx or Apache
in front of PHP-FPM gives you multiple worker processes, so one slow
request (a large report page, a big search) doesn't block every other
staff member's requests the way a single-threaded dev server would. If
this is a NativePHP desktop build instead of a server deployment, this
doesn't apply - NativePHP manages its own embedded server.

**Enable OPcache.** Without it, PHP recompiles every file on every
request - real, avoidable overhead on every single page load.
`php83 -m | grep -i opcache` to check; install `php83-php-opcache` if
missing.

**Cache config, routes, and views** as part of your deploy step:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

(Skip these in local dev - they make `.env`/route/view changes stop
taking effect until you clear them again, which is exactly why the setup
steps above don't include them.) Remember: `config:cache` is also why
`ProductionSeeder` reads settings via `config('clinic.*')` instead of
`env()` directly - see the changelog below.

**Keep a queue worker running continuously.** Backup generation
(`GenerateBackupJob`) needs one, or backups will sit at "Generating…"
forever. In production, run it under a process supervisor rather than a
plain terminal command, so it restarts automatically if it crashes or the
server reboots. Example `systemd` unit
(`/etc/systemd/system/zedan-queue.service`):

```ini
[Unit]
Description=Zedan queue worker
After=network.target

[Service]
User=www-data
WorkingDirectory=/path/to/zedan
ExecStart=/usr/bin/php83 artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now zedan-queue
```

(`--max-time=3600` makes the worker gracefully restart hourly, which
picks up code deploys automatically instead of serving stale cached
classes indefinitely.) On Windows/NativePHP, the equivalent is a
Scheduled Task or Windows Service running the same `queue:work` command.

If you'd rather not manage a worker process at all, set
`QUEUE_CONNECTION=sync` in `.env` - backups then generate immediately/
synchronously again (same as before this pass), just without background
processing.

### Outgrowing SQLite

SQLite (with WAL mode, already configured - see `config/database.php`)
comfortably handles a single clinic's real-world concurrent load: a
handful of staff clicking around at once, occasional simultaneous writes.
What it *won't* do well is many simultaneous writers under sustained
heavy load, or true multi-server/multi-clinic deployment, since SQLite is
one file with connection-level write serialization no matter how it's
tuned.

The schema was written to make switching painless when you actually hit
that ceiling: every migration avoids SQLite-only syntax, and
`config/database.php` already has a ready-to-use `mysql` connection. The
switch is four `.env` lines (`DB_CONNECTION=mysql` plus host/database/
username/password), then:

```bash
php artisan migrate --force
```

against the fresh MySQL database. There's no SQLite-specific logic
anywhere in the application code to unwind - the double-booking conflict
check, for instance, already uses `lockForUpdate()`, which is a no-op on
SQLite but a real row lock on MySQL, so it becomes *more* protective
after the switch, not less.

## Resolved assumptions

The original project brief was assembled from four overlapping drafts.
Where they disagreed, these are the defaults this codebase implements:

- **Backup access:** only the Doctor can trigger or delete a backup;
  Receptionist is limited to viewing/downloading history.
- **Patients:** a genuine first-class module with its own table and full
  CRUD (Doctor full access, Receptionist create/edit, no delete) — not just
  a view derived from appointments.
- **Roles/permissions:** `spatie/laravel-permission` handles role storage;
  Laravel Policies + route middleware handle enforcement.
- **Theme (light/dark):** stored on the `users` table (`theme` column), not
  `localStorage`, so it follows a user across devices/browsers.
- **Patient age:** stored as either `date_of_birth` (preferred, used to
  compute a live age) or a manually entered `age` integer when the date of
  birth isn't known — both nullable, the form accepts either.
- **Doctors vs. login accounts:** the `doctors` table is a standalone
  clinical roster (name, specialty, phone, hours) with a nullable
  `user_id`. The seeded admin Doctor is linked to a login; a second
  clinical doctor exists purely for scheduling and never logs in — this
  supports a clinic where not every doctor needs their own account.

## Project structure highlights

- `app/Models` — Eloquent models. Business logic lives here where it's
  reusable: `Appointment::findConflict()` for double-booking checks,
  `Payment::recalculate()` for balance/status after installments,
  `InventoryItem::isLowStock()`.
- `app/Policies` — one policy per module, matching the Doctor/Receptionist
  permission table in the spec.
- `app/Http/Requests` — all validation, including the phone-duplicate check
  on patients and the payment-type amount rules (Paid Now = full cost, Pay
  Later = 0, Installment first payment > 0 and < total).
- `app/Services/BackupService.php` — builds the full dataset once and
  produces PDF, Excel, or both (zipped) from it; used by both the manual
  "Backup Now" button and the scheduled command.
- `resources/views` — Blade views. Almost everything create/edit related is
  a Bootstrap modal submitted via `data-ajax-form` (see
  `resources/js/app.js`) so validation errors show inline without a full
  page reload; **Login, User Management, and Profile are full pages**, per
  spec.
- `lang/en`, `lang/ar` — all UI strings, validation messages, and auth
  messages are translated in both; Arabic also flips the whole layout to
  RTL via a separate `bootstrap.rtl.min.css` Vite entry and the `dir`
  attribute on `<html>`.

## Testing

```bash
php artisan test
```

Included feature/unit tests cover:
- Login/logout, wrong password, deactivated account, unauthenticated
  redirect (`tests/Feature/AuthTest.php`)
- Role-based access restrictions — Receptionist blocked from Doctor-only
  actions (`tests/Feature/RoleAccessTest.php`)
- Appointment double-booking prevention for both doctor and room, including
  that cancelled appointments don't block new bookings
  (`tests/Feature/AppointmentConflictTest.php`)
- Payment balance/status recalculation across installments
  (`tests/Unit/PaymentRecalculationTest.php`)

## Changelog — fixes since the initial version

The first draft of this codebase was hand-written without ever being
executed (see below). Running it for real surfaced several genuine bugs,
now fixed:

- **`config/app.php` had a custom `providers` array** listing only the
  three third-party packages, which silently broke Laravel's own core
  provider registration (`Target class [files] does not exist`). Removed
  — those packages auto-discover already.
- **`PaymentRequest` required a `patient_id` field the form never sent**
  (the patient comes from the route, `/patients/{patient}/payments`, not
  the form body) — every payment submission failed validation silently.
  Fixed by removing that rule; also hardened `app.js` so a validation
  error with no matching form field now shows a fallback alert instead of
  failing with no visible feedback at all.
- **Appointment date comparisons used `where()` instead of `whereDate()`**
  — Laravel's `date` cast stores SQLite values as `Y-m-d H:i:s`, so a bare
  `where('appointment_date', '2026-07-29')` never matched. This broke both
  the daily schedule view *and* the double-booking conflict check (which
  meant conflicting appointments weren't actually being blocked). Fixed in
  `Appointment::findConflict()` and `Appointment::scopeForDate()`.
- **Dark mode used a near-black background with the same vivid accent
  blue as light mode**, reading as harsh/glowing. Softened both in
  `resources/css/theme.css`.
- **A packaged desktop build (NativePHP) shipped with no users at all**
  — see "Packaged desktop builds" above for the fix.
- **A leftover migration was seeding a hardcoded `doctor`/`password`
  account**, silently overriding the configurable `ProductionSeeder`
  approach above since migrations run first. Removed.
- **The first-launch provisioning check in `AppServiceProvider` was
  running two database queries on every single request, forever** — not
  just the first one. Replaced with a cheap file-marker check
  (`storage/framework/.db_provisioned`) so steady-state requests pay
  almost nothing.

A general solidity/scalability pass also added: a DB transaction around
payment + first-installment creation (`PaymentController`), error handling
in `BackupService` that cleans up partially-written files instead of
leaving orphans, pagination on the Treatments and Inventory pages (both
previously loaded every row unbounded), indexes on `appointments.status`,
`payments.status`, and `treatments.category`, and a standard
`composer run dev` script (Windows-safe variant, see "Local setup" above).

### Heavy-load hardening pass

Prompted by "this needs to survive real production load in a busy
clinic," a further round specifically targeting concurrent-user and
data-volume scale:

- **SQLite WAL mode + busy timeout** (`config/database.php`) - the
  single highest-leverage change for handling more than one person
  writing at once; see "Outgrowing SQLite" above for where this still
  has a ceiling.
- **Session and cache moved off the database driver to file-based** -
  every request no longer writes a session row to the same SQLite file
  as actual clinic data.
- **Backup generation is now a queued background job**
  (`GenerateBackupJob`), not synchronous in the web request. A data-heavy
  clinic's full export could otherwise take a long time and tie up the
  request the whole while. The Backups page now shows a real
  queued/completed/failed status per entry. Requires a running queue
  worker in production - see "Production deployment" above.
- **`BackupService` now streams data via `cursor()` instead of loading
  every row into memory at once** - keeps memory flat regardless of how
  many years of appointments/payments/patients have accumulated.
- **The double-booking conflict check now has a real atomicity
  guarantee**, not just a friendly pre-check. `AppointmentController`
  wraps create/update in a transaction with a locked re-check
  immediately before the write - a no-op on SQLite (which already
  serializes writes) but a genuine row lock on MySQL, closing a real race
  window that existed before.
- **Uploaded photos are now resized to a 1000px max dimension** before
  storage (`HandlesImageUploads` trait, used by every controller that
  accepts a photo) - previously stored untouched, meaning a multi-MB
  phone photo was downloaded in full just to render a 32px thumbnail.
  Falls back to storing the original if image processing fails for any
  reason, rather than losing the upload.
- **Documented an actual production deployment path** (OPcache, config/
  route/view caching, a real web server instead of `artisan serve`,
  supervising the queue worker, and when to move off SQLite to MySQL) -
  see "Production deployment" above.

### "Make it feel real" pass

Prompted by hands-on use turning up rough edges that don't show up in a
code read-through:

- **Doctors, Rooms, and Treatments could only be deactivated, never
  reactivated** - `destroy()` was one-way with no way back short of
  editing the database directly. Added `reactivate()` for all three, with
  a button that appears in place of "deactivate" once a record is
  inactive.
- **`UserManagementController::destroy()` was dead code** - a full
  method + route that no view ever called, left over from an earlier
  iteration (`toggleActive()` already fully covers activate/deactivate
  for staff). Removed rather than leaving an unreachable, confusing
  duplicate.
- **Every quick action (delete, deactivate, reactivate, toggle) now goes
  through the same AJAX path as create/edit forms** - previously these
  were plain form submissions causing a full browser navigation on every
  click. Login, logout, and the User Management/Profile full pages are
  deliberately left as classic submissions - that's the intended
  behavior for those, not an oversight.
- **`app.js`'s fallback alert strings ("Something went wrong", "Network
  error") were hardcoded English** even though every other message in
  the app is bilingual. Now sourced from a small `window.i18n` object
  populated from Blade, so nothing in the JS is English-only anymore.
- **Backups had no way to actually restore data** - PDF/Excel are
  reports, not restorable snapshots. Added a "Database" backup type (raw
  SQLite file copy) and a `backup:restore` command - deliberately a CLI
  operation with the app stopped, not a live web button, since hot-
  swapping a database file under a running app risks corruption in a way
  that isn't safe to ship unverified. See "Automatic monthly backups"
  above.
- **Added comprehensive activity logging** (`spatie/laravel-activitylog`)
  across every CRUD action plus login/logout/failed-login, with a
  Doctor-only viewer page (filterable, with an old/new value diff per
  entry). See "Activity log" above.

## Honest note on how this was built

This codebase was written by hand against Laravel 12 / Spatie /
DomPDF / Maatwebsite-Excel conventions, but **it has not been executed** —
it was produced in an environment without PHP, Composer, or Packagist
access, so none of the commands above have actually been run against it.
Treat the first `composer install && php artisan migrate --seed && php
artisan test` locally as a shake-out pass rather than an assumption that
everything is already verified. Likely first-run issues to watch for:

- A missing `use` import or a typo in a rarely-hit code path.
- Package version drift — `composer.json` pins compatible major versions,
  but the exact resolved versions of `maatwebsite/excel` or
  `barryvdh/laravel-dompdf` may need a config adjustment.
- `npm run build` needs Node 18+; if the RTL Bootstrap import path differs
  between installed versions, check `resources/css/app-rtl.css`.
- **Scope note:** the spec asks for a patient "quick-create inline" option
  directly inside the appointment-booking modal, with name/phone/age/address
  auto-filling from the selected patient. This build lets you pick any
  existing patient from a dropdown when booking, and patients are created
  from the Patients page — the inline quick-create-with-autofill inside the
  appointment modal itself would need a small additional JS/AJAX lookup
  that wasn't added here to keep the delivered code within what could be
  reviewed carefully by hand. It's a reasonable next addition.

If something doesn't run cleanly, the fix is almost always local and small
— the architecture (schema, policies, routes, business logic) is the part
most worth trusting; the last few percent of polish is what a real local
run would surface.
"dental-system" 
