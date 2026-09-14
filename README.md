# Livestock Fattening ERP — Laravel Backend

Covers cattle, goat, and sheep fattening operations. Built around **batches**
(lots of animals bought together and fed toward a sale target) as the core
unit for profitability reporting.

## Stack

- Laravel 13, SQLite (local dev), Sanctum token auth
- `database/migrations/` — 23 domain migrations (roles/permissions, species,
  pens, batches, animals, weigh-ins, feed items/rations/logs, health
  records, movements, purchase/sales orders, expenses, payments) plus
  Sanctum's `personal_access_tokens` table.
- `app/Models/` — Eloquent models with relationships **and** the business-logic
  methods that compute KPIs on the fly rather than storing stale numbers:
  - `Animal::averageDailyGainKg()`, `latestWeightKg()`, `isReadyToSell()`
  - `Batch::feedConversionRatio()`, `netProfit()`, `daysOnFeed()`
- `app/Http/Controllers/Api/` — controllers for the actual fattening workflow:
  intake (`AnimalController@store`), weigh-ins, feed logging, health records,
  and the **sales/settlement flow** (`SalesOrderController@store`), which:
  1. Creates the sales order + line items
  2. Marks each sold animal `status = sold` with exit weight/date
  3. Auto-closes a batch once every animal in it is sold or dead
- `routes/api.php` — public `POST /login`, everything else under `auth:sanctum`.

## Getting started

```bash
composer install
php artisan migrate --seed
php artisan serve
```

Seeded starter data (`database/seeders/`):
- **Roles/permissions** — Admin, Farm Manager, Feeder, Vet, Sales
- **Species defaults** — Cattle, Goat, Sheep (cycle days, target ADG/entry/exit weights)
- **Pens** — a quarantine pen, two growing pens, two finishing pens
- **Admin user** — `admin@livestock-erp.test` / `password`

## Auth

```bash
POST /api/login   { "email": "admin@livestock-erp.test", "password": "password" }
# -> { "user": {...}, "token": "..." }
```

Send the token as `Authorization: Bearer <token>` on every other endpoint.
`POST /api/logout` revokes the current token.

## Not included (add as it grows)

- Form request classes (validation is inline for readability — move to
  `FormRequest` classes as it grows)
- Policy classes for RBAC enforcement (currently just a `hasPermission()`
  helper on `User` — wire it into route middleware or policies)
- Seed data for feed items, suppliers, customers, warehouses (create these
  through the API, or add seeders, before exercising feed logging or sales)
