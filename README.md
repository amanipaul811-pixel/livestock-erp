# Livestock Fattening ERP

Covers cattle, goat, and sheep fattening operations. Built around **batches**
(lots of animals bought together and fed toward a sale target) as the core
unit for profitability reporting.

## Stack

- Laravel 13, SQLite (local dev), Sanctum token auth (API) + session auth (web UI)
- `database/migrations/` — 24 domain migrations plus Sanctum's `personal_access_tokens` table
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

## Getting started

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

`permission:<code>` middleware sits on the write routes that have a matching
seeded permission (`batch.create`, `animal.create`, `weighin.create`,
`feedlog.create`, `healthrecord.create`, `expense.create`,
`rationformula.create`, `animalmovement.create`, `salesorder.create`,
`purchaseorder.create`/`.update`, `supplier.create`, `warehouse.create`,
`dashboard.view` — API only, since the web dashboard is the post-login
landing page for every role). A 403 names the missing permission.

Not wired to anything yet: `sale.approve` and `user.manage` are seeded but
have no corresponding action in the app (no sale-approval step, no user
management UI). Payments (sales or purchase order) aren't permission-gated —
any authenticated user can record one.

## Not included (known gaps)

- Sale/purchase-order approval workflow, user management UI
- CRUD for suppliers/warehouses is create-only (no edit/delete)
- Web forms don't hide actions a role lacks permission for — a Vet can open
  the "New Batch" form and only finds out at submit (403)
