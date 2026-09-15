# Livestock Fattening ERP

Covers cattle, goat, and sheep fattening operations. Built around **batches**
(lots of animals bought together and fed toward a sale target) as the core
unit for profitability reporting.

## Stack

- Laravel 13, PostgreSQL (dev and prod — target deploy is Render + managed
  Postgres), Sanctum token auth (API) + session auth (web UI)
- `database/migrations/` — 24 domain migrations plus Sanctum's `personal_access_tokens` table
- `database/factories/` — a factory per domain model, used by the test suite
- `app/Models/` — Eloquent models with relationships **and** the business-logic
  methods that compute KPIs on the fly rather than storing stale numbers:
  - `Animal::averageDailyGainKg()`, `latestWeightKg()`, `isReadyToSell()`
  - `Batch::feedConversionRatio()`, `netProfit()`, `daysOnFeed()`
  - `SalesOrder`/`PurchaseOrder::amountPaid()`, `balanceDue()`
  - `RationFormula::dailyCostPerHead()`
- Two parallel interfaces over the same models:
  - `app/Http/Controllers/Api/` — Sanctum-token API (`routes/api.php`)
  - `app/Http/Controllers/Web/` — session-auth Blade UI, Tailwind + Alpine via
    CDN, no JS build step (`routes/web.php`, `resources/views/`)
- `app/Http/Requests/{Api,Web}/` — all validation lives in FormRequest classes,
  one per write endpoint
- `app/Http/Middleware/EnsurePermission.php` — RBAC gate (`permission:<code>`
  middleware) backed by `User::hasPermission()`
- `tests/` — 85 tests (feature tests per resource + unit tests for every KPI
  calculation); run with `composer test` or `php artisan test`

## Workflow coverage

- **Buy** — create a batch, intake animals into it (or raise a formal
  purchase order against a supplier and track its payments)
- **Feed** — log feed against a batch, or define a ration formula (feed mix
  + daily kg/head) and see its cost per head
- **Grow** — weigh-ins per animal, pen movements with history, health
  records (a `death` record auto-retires the animal)
- **Sell** — multi-animal sales order; settlement auto-marks animals `sold`
  and closes the batch once nothing is left `on_feed`; record payments
  against the balance (capped server-side — can't overpay)
- **Overhead** — batch-level expenses (labor, utilities, transport, rent)
  factor straight into `netProfit()`
- **Admin** — manage staff accounts and roles at `/users` (gated by
  `user.manage`)

Every master-data resource (batches, suppliers, warehouses, customers, feed
items, ration formulas) supports full create/edit/delete through the web UI,
with delete blocked server-side (a friendly error, not a DB constraint crash)
when dependent records exist.

## Getting started

Requires a local PostgreSQL instance (dev intentionally mirrors the Render +
managed Postgres production target — see `.env.example` for the connection
vars to fill in).

```bash
composer install
php artisan migrate --seed
php artisan serve
```

Web UI at `/` (redirects to `/login`). API base is `/api`.

Seeded starter data (`database/seeders/`):
- **Roles/permissions** — Admin, Farm Manager, Feeder, Vet, Sales (see RBAC below)
- **Species defaults** — Cattle, Goat, Sheep (cycle days, target ADG/entry/exit weights)
- **Pens** — a quarantine pen, two growing pens, two finishing pens
- **Admin user** — `admin@livestock-erp.test` / `password`

Feed items, customers, suppliers, and warehouses have no seed data — add them
through the UI (`/feed-items`, `/customers`, `/suppliers`, `/warehouses`) or
API before exercising feed logging, sales, or purchase orders.

## Auth

**API** (Sanctum tokens):
```bash
POST /api/login   { "email": "admin@livestock-erp.test", "password": "password" }
# -> { "user": {...}, "token": "..." }
```
Send the token as `Authorization: Bearer <token>` on every other endpoint.
`POST /api/logout` revokes the current token.

**Web**: standard session login at `/login`, separate guard from the API but
the same `User` model and credentials.

## RBAC

`permission:<code>` middleware sits on every route with a matching seeded
permission (`batch.create`/`.update`, `animal.create`, `weighin.create`,
`feedlog.create`, `healthrecord.create`, `expense.create`,
`rationformula.create`, `animalmovement.create`, `salesorder.create`,
`purchaseorder.create`/`.update`, `supplier.create`, `warehouse.create`,
`user.manage`, `dashboard.view` — API only, since the web dashboard is the
post-login landing page for every role). A 403 names the missing permission.

This applies to the `GET` create/edit routes too, not just the write
endpoints — a role without the permission is turned away before it can fill
out a form it isn't allowed to submit. The nav and page-level buttons
additionally hide anything the current user can't act on, as a UX layer on
top of that -- but the routes themselves are what actually block it.

Not wired to anything: `sale.approve` was removed from the seed list (no
approval step exists in this workflow — sales settle immediately on
creation). Payments (sales or purchase order) aren't permission-gated — any
authenticated user can record one.

## Not included (known gaps)

- No sale/purchase-order approval workflow (by design — see RBAC above)
- Password reset flow (no forgot-password UI; an admin can reset a user's
  password from `/users`)
- Rate limiting on login, Sanctum token expiration policy
