# Deploying to Render

This wasn't buildable/testable locally (no Docker on the dev machine, no
Render account access from here) -- these steps are written from
well-established Laravel + Docker + Render patterns, but the very first
deploy is where you'll actually find out if anything needs a tweak. Render's
build logs are the place to look if it doesn't come up clean.

## What's here

- `Dockerfile` -- two-stage build: Composer installs dependencies, then a
  `php:8.3-fpm` runtime image with Nginx + PHP-FPM run together via
  Supervisor (Render web services run one container, so both need to live in it)
- `docker/nginx.conf` -- templated Nginx config (`${PORT}` gets substituted
  at container start, since Render assigns the port dynamically and Nginx
  config files can't read env vars directly)
- `docker/entrypoint.sh` -- renders the Nginx template, runs
  `migrate --force` + the three `:cache` commands, then starts Supervisor
- `render.yaml` -- a Render Blueprint: provisions the web service and a
  managed Postgres database together, wiring `DB_*` automatically

## One-time setup

1. **Push this repo to GitHub** (or GitLab) -- Render deploys from a git
   remote, not a local directory.

2. **Generate an APP_KEY** locally -- don't let Render generate one for you,
   and don't reuse the one in your local `.env`:
   ```bash
   php artisan key:generate --show
   ```
   Save the `base64:...` output; you'll paste it into Render in step 4.

3. **In the Render dashboard**: New → Blueprint → connect the GitHub repo →
   Render reads `render.yaml` and shows you the two resources it's about to
   create (the `livestock-erp` web service and the `livestock-erp-db`
   database). Confirm.

4. **Fill in the secrets Render will prompt for** (anything marked
   `sync: false` in `render.yaml`):
   - `APP_KEY` -- the value from step 2
   - `APP_URL` -- Render gives you the service's `.onrender.com` URL after
     the first deploy; come back and set this once you have it (or set your
     custom domain here once that's attached -- see below)
   - `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`,
     `MAIL_FROM_ADDRESS` -- real SMTP credentials. Without these, the
     forgot-password flow has no way to actually send an email (it'll still
     "succeed" from the user's point of view, but the message goes nowhere)

5. **Deploy.** Watch the build log. If the Docker build fails, it's almost
   always a missing PHP extension or an apt package name mismatch -- both
   are one-line fixes in the `Dockerfile`.

   One specific thing to check if Render rejects `render.yaml` itself
   (before the build even starts): the key marking this as a Docker service
   is `env: docker` here. Render has used both `env` and a newer `runtime`
   key across different versions of the Blueprint spec, and I couldn't
   verify which your account's Render expects without live access -- if the
   Blueprint preview errors on that line, try `runtime: docker` instead.

6. **Seed starter data** once the first deploy is live, from Render's shell
   (Dashboard → your service → Shell):
   ```bash
   php artisan db:seed --force
   ```
   This is deliberately not automatic -- re-running seeders against a
   database that already has real data is a much worse mistake than typing
   one extra command.

## Custom domain + HTTPS

Render → your service → Settings → Custom Domain. Add your domain, point its
DNS at Render per the instructions Render shows, and Render issues a TLS
certificate automatically once DNS resolves. Update the `APP_URL` env var to
match once it's attached.

## Known simplifications (fine at current scale, revisit if it grows)

- **Migrations run on every container start** (inside `entrypoint.sh`), not
  as a separate pre-deploy step. Harmless on a single instance since
  migrations are idempotent, but if this ever scales to multiple web
  instances, move `php artisan migrate --force` into Render's **Pre-Deploy
  Command** setting instead (Dashboard → service → Settings) and drop it
  from `entrypoint.sh` -- otherwise every instance races to migrate on boot.
- **`SESSION_DRIVER`/`CACHE_STORE` are `file`**, which only works correctly
  for a single running instance -- fine for the `starter` plan's one
  instance. If this ever scales horizontally, switch both to `database` in
  the Render env vars so state is shared instead of stuck per-container.
- **No connection pooling** in front of Postgres. Not needed at the
  scale of one farm's internal tool; if concurrent users grow substantially,
  look at Render's PgBouncer add-on or a pooled connection string.
- **Database backups**: confirm they're actually on after the first deploy
  (Render → your database → Backups) -- the `starter` plan includes them,
  but it's worth a look rather than assuming. Trigger a manual restore once,
  somewhere non-production, so "we have backups" is a verified fact and not
  a hope.
