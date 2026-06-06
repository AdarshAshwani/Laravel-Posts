# Postify — Installation Guide

**Application:** Postify (a multi‑user blog / posts CMS)
**Latest release tag:** v1.00.010
**Framework:** Laravel 12 (PHP 8.2+)
**Last updated:** 2026‑06‑06

This document explains the system requirements and the step‑by‑step procedure to
install and run **Postify** on a local machine (XAMPP / Windows) or a
remote server (Linux).

The application ships with a **built‑in web installer** (a setup wizard at
`/install`) that configures the database and creates the first admin account, so
in most cases you will **not** need to edit the database or run migrations by
hand. Three installation paths are documented:

- **Method A** — XAMPP / Windows using the web installer (recommended for local use)
- **Method B** — Manual / command line (for developers)
- **Method C** — Production Linux server from a release tarball (no Composer/npm bootstrap needed)

---

## Table of Contents

1. [What you are installing](#1-what-you-are-installing)
2. [System requirements](#2-system-requirements)
3. [Required PHP extensions](#3-required-php-extensions)
4. [Before you begin (pre‑installation checklist)](#4-before-you-begin-pre-installation-checklist)
5. [Installation — Method A: XAMPP / Web Installer (recommended)](#5-installation--method-a-xampp--web-installer-recommended)
6. [Installation — Method B: Manual / Command Line (developers)](#6-installation--method-b-manual--command-line-developers)
7. [Installation — Method C: Production Linux Server (release tarball)](#7-installation--method-c-production-linux-server-release-tarball)
8. [The web installer walkthrough](#8-the-web-installer-walkthrough)
9. [Serving the application](#9-serving-the-application)
10. [Environment (.env) configuration reference](#10-environment-env-configuration-reference)
11. [Verifying the installation](#11-verifying-the-installation)
12. [Re‑running / resetting the installer](#12-re-running--resetting-the-installer)
13. [Troubleshooting](#13-troubleshooting)

---

## 1. What you are installing

Postify is a content‑management / blogging application. Its main features are:

| Area | Capability |
|------|------------|
| **Posts** | Create / edit / delete posts, SEO‑friendly slugs, full‑text search |
| **Media** | Image / file attachments per post (`post_media`) |
| **Users & Profiles** | Profile page, avatar upload, social links, multiple "sub‑profiles" |
| **Access control** | Roles & permissions (RBAC), admin flag, login / logout |
| **Site settings** | Branding settings (site name, logo, etc.) editable in the admin area |
| **Auditing** | Audit logs and activity logs |
| **Installer** | Self‑contained web setup wizard with an installer lock |

Data such as sessions, cache, and queue jobs is stored in the **database** by
default (see `.env`).

---

## 2. System requirements

| Software | Minimum version | Notes |
|----------|-----------------|-------|
| **PHP** | **8.2** or higher | Required by `composer.json` (`"php": "^8.2"`) |
| **Composer** | 2.x | PHP dependency manager — <https://getcomposer.org>. *Not needed for Method C* (vendor is bundled). |
| **MySQL** **or** **MariaDB** | MySQL **5.7+/8.0** · MariaDB **10.3+** | **Required.** The app uses a MySQL/MariaDB `FULLTEXT` index and the installer connects with the `mysql` driver. SQLite is **not** supported for this app even though it appears in `config/database.php`. |
| **Node.js** | **20.19+** (or 22.12+) | Needed to run `npm install` / `npm run build` (Vite 7). Older versions fail the build. LTS recommended — get it from [nodejs.org](https://nodejs.org). Verify with `node -v`. |
| **npm** | 10+ | Ships with Node.js |
| **Web server** | Apache (XAMPP) or PHP built‑in server | Apache `mod_rewrite` must be enabled if serving via Apache |
| **Git** | any recent | Optional — only if cloning from a repository |

> **Windows users:** [XAMPP](https://www.apachefriends.org/) 8.2+ bundles a
> compatible **Apache + MySQL (MariaDB) + PHP 8.2**. You still need to install
> **Composer** and **Node.js** separately — XAMPP does not include them.

### Tech stack at a glance

- **Backend:** Laravel 12, Laravel Tinker
- **Frontend build:** Vite 7, Tailwind CSS 4, Axios, PostCSS, Autoprefixer
- **Dev tooling:** Pint, Pail, Sail, PHPUnit 11, Mockery, Faker, Collision

---

## 3. Required PHP extensions

These are required (most are enabled by default in XAMPP / standard PHP builds):

- `pdo_mysql` **(required — database driver)**
- `mbstring`
- `openssl`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `bcmath`
- `fileinfo` **(required — used for file/image uploads)**
- `curl`
- `gd` *(recommended — image handling for avatars / post media)*

**Verify your PHP version and extensions:**

```bash
php -v
php -m
```

> In XAMPP, extensions are toggled in `C:\xampp\php\php.ini`. Make sure the lines
> for `extension=pdo_mysql`, `extension=fileinfo`, `extension=mbstring`,
> `extension=gd`, and `extension=curl` are **not** commented out (no leading `;`),
> then restart Apache.

---

## 4. Before you begin (pre‑installation checklist)

1. **Install PHP 8.2+, Composer, MySQL/MariaDB, and Node.js 20.19+ (or 22.12+)** (or install XAMPP + Composer + Node.js).
2. **Place the project** in your web root. On XAMPP this is typically:
   ```
   C:\xampp\htdocs\Laravel-Posts
   ```
3. **Start MySQL** (e.g. from the XAMPP Control Panel → *Start* next to *MySQL*).
4. **Create an empty database.** The installer migrates into an existing
   database but does **not** create the database itself.

   Using phpMyAdmin (`http://localhost/phpmyadmin`) → **New** → create a
   database, e.g. **`laravel_posts`** with collation `utf8mb4_unicode_ci`.

   Or via the MySQL command line:
   ```sql
   CREATE DATABASE laravel_posts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
5. **Note your DB credentials** — host (`127.0.0.1`), database name
   (`laravel_posts`), username (`root` on XAMPP), and password (blank by default
   on XAMPP). You will enter these in the installer.

---

## 5. Installation — Method A: XAMPP / Web Installer (recommended)

Use this path for a normal local deployment. The web installer handles the
database migration and admin creation for you.

### Step 1 — Get the code into the web root
Copy or clone the project so it lives at `C:\xampp\htdocs\Laravel-Posts`.

```bash
# if cloning
cd C:\xampp\htdocs
git clone https://github.com/AdarshAshwani/Laravel-Posts.git
```

### Step 2 — Install PHP dependencies
From the project folder:

```bash
cd C:\xampp\htdocs\Laravel-Posts
composer install
```

### Step 3 — Install & build front‑end assets
> **Requires Node.js 20.19+ or 22.12+** (Vite 7 refuses to build on older
> versions) — check with `node -v` first. If Node is missing or too old, install
> the latest LTS from <https://nodejs.org>.

```bash
node -v          # must be >= 20.19  (or >= 22.12)
npm install
npm run build
```
> `npm run build` compiles the Tailwind/Vite assets into `public/build`. For
> active development you can instead run `npm run dev` (see
> [Serving the application](#9-serving-the-application)).

### Step 4 — Create the environment file
If a `.env` file does not already exist, create it from the template and
generate an app key:

```bash
copy .env.example .env        # Windows (PowerShell/CMD)
# cp .env.example .env        # macOS/Linux

php artisan key:generate
```

> A `.env` already ships in this project. If you are setting up a **fresh** copy
> and `APP_KEY` is empty, you **must** run `php artisan key:generate`.

### Step 5 — Create the storage symlink (for uploaded files)
Avatars and post media are stored on the `public` disk and served via a symlink:

```bash
php artisan storage:link
```
This creates `public/storage` → `storage/app/public`. Without it, uploaded
images will appear broken.

### Step 6 — Start the servers
Start **Apache** and **MySQL** in the XAMPP Control Panel
*(or* run `php artisan serve` — see [Section 9](#9-serving-the-application)).

### Step 7 — Run the web installer
Open the app in your browser. The app will automatically redirect you to the
installer the first time:

```
http://localhost/Laravel-Posts/public/install
```
*(or your configured URL — see [Section 9](#9-serving-the-application) for clean URLs.)*

Follow the wizard ([detailed in Section 8](#8-the-web-installer-walkthrough)):
**Welcome → Database → Admin account → Done.** When it finishes you will be
logged in as the administrator and the installer locks itself.

✅ **Installation complete.**

---

## 6. Installation — Method B: Manual / Command Line (developers)

Use this if you prefer to configure everything by hand and bypass the web
wizard. (You can still use the wizard later — it is the recommended path for
non‑developers.)

```bash
# 1. Install dependencies   (npm needs Node.js 20.19+ or 22.12+ — check: node -v)
composer install
npm install

# 2. Environment
copy .env.example .env        # Windows  (cp on macOS/Linux)
php artisan key:generate

# 3. Configure the database in .env  (see Section 10)
#    Set DB_CONNECTION=mysql and your DB_DATABASE / DB_USERNAME / DB_PASSWORD

# 4. Create the database schema
php artisan migrate

# 5. (Optional) seed sample data
php artisan db:seed

# 6. Link storage for uploaded files
php artisan storage:link

# 7. Build front-end assets
npm run build

# 8. Serve the app
php artisan serve
```

> **Skipping the web installer with the manual path:** the application gates
> itself behind the installer until a lock file exists at `storage/installed`.
> If you migrate by hand and want to go straight to `/login`, create the lock
> file manually:
> ```bash
> # Windows PowerShell
> New-Item -ItemType File storage\installed -Value "installed"
> # macOS/Linux
> echo "installed" > storage/installed
> ```
> You will then need to create an admin user yourself (e.g. via
> `php artisan tinker`), since the wizard normally does that step.

---

## 7. Installation — Method C: Production Linux Server (release tarball)

This is the fastest way to deploy to a remote Linux server. It downloads a
**release tag** of the project, which is published as a **self‑contained
bundle** — it already includes `vendor/`, `node_modules/`, `public/build`, and a
starter `.env`. Because of that, **you do not run `composer install` or copy
`.env.example`** on the server.

> **Why `npm install` is still required:** the bundled `node_modules` is built on
> Windows, so its native binaries (esbuild / rollup) won't run on Linux. Running
> `npm install` re‑fetches the correct Linux‑native binaries, and `npm run build`
> regenerates the assets for this server.

### Full procedure

```bash
# 1. Create a working folder and download the release tarball
mkdir -p stories && cd stories
wget https://github.com/AdarshAshwani/Laravel-Posts/archive/refs/tags/v1.00.010.tar.gz

# 2. Extract it
tar -xvzf v1.00.010.tar.gz

# 3. Move the extracted contents (visible files + the 7 tracked dotfiles) up one level
cd Laravel-Posts-1.00.010
mv * ..
mv .editorconfig .env .env.example .gitattributes .gitignore .htaccess .version ..
cd ..                           # ← IMPORTANT: step back up; artisan now lives here

# 4. Remove the (now empty) extracted folder and the tarball
rm -rf Laravel-Posts-1.00.010 v1.00.010.tar.gz

# 5. App key + storage symlink
php artisan key:generate        # generates a fresh APP_KEY for THIS server
php artisan storage:link        # creates public/storage -> storage/app/public

# 6. Rebuild front-end assets for this server's platform  (server needs Node.js 20.19+ / 22.12+)
npm install                     # fixes Linux-native binaries (node_modules came from Windows)
npm run build

# 7. Permissions & ownership
chmod -R 755 .                  # directories traversable, files readable
chmod 600 .env                  # keep secrets private (APP_KEY + DB credentials)
chown -R daemon:daemon .        # set owner to YOUR web-server user (see note below)

# 8. Point your web server / vhost at this folder, then open it in a browser
#    and complete the web installer at /install  (see Sections 8 & 9)
```

### Important notes & corrections

- 🔴 **The `cd ..` in step 3 is essential.** After `mv * ..` the shell is still
  inside the now‑empty `Laravel-Posts-1.00.010/`. Without stepping back up,
  `rm -rf …` targets the wrong paths and every `php artisan …` command fails with
  *"Could not open input file: artisan"* (because `artisan` was moved to the
  parent). The original snippet omitted this line.
- 🔐 **`chmod -R 755 .` leaves `.env` world‑readable.** `755` = `rwxr‑xr‑x`, so
  any other OS user can read your `APP_KEY` and database credentials. Always
  follow it with **`chmod 600 .env`** (the example above does). The bundled
  `.htaccess` already blocks `.env` over HTTP, but that does not protect it at
  the filesystem level on shared hosts.
- 👤 **`daemon:daemon` is host‑specific.** `daemon` is the Apache user on some
  shared/XAMPP‑Linux hosts, but it is **`www-data`** on Debian/Ubuntu and
  **`apache`** on RHEL/CentOS/Alma. Set the owner to whatever user your web
  server (Apache/PHP‑FPM/nginx) actually runs as, or the app cannot write to
  `storage/` and `bootstrap/cache`.
- 📁 **Serving path / clean URLs — automatic.** The bundled `.htaccess` no longer
  hardcodes a `RewriteBase`, so the app serves correctly from **any** folder name
  (or the domain root) with no edit, and `/public` never appears in the URL. Just
  drop the project under the web root (or point a vhost `DocumentRoot` at this
  folder) and open `/install`. See [Section 9](#9-serving-the-application).
- 🗄️ **Database still uses the web installer.** This bundle does **not** include a
  database. The starter `.env` is overwritten by the installer's Database step,
  which runs `migrate:fresh` and writes your real credentials. Create an **empty**
  MySQL/MariaDB database first ([Section 4](#4-before-you-begin-pre-installation-checklist)),
  then complete `/install`.
- 🧹 **Hidden directories are not moved.** Only the 7 listed dotfiles are moved up;
  directories like `.github/` are intentionally left behind and deleted with the
  cleanup in step 4 — fine for a server (no CI needed there).
- 🔁 **Updating to a newer tag** later: re‑run this procedure into a fresh folder
  (or `git pull` if you deploy via clone), then `npm install && npm run build`
  and `php artisan optimize:clear`. Do **not** delete `storage/installed` unless
  you intend to re‑run the installer.

---

## 8. The web installer walkthrough

The installer is a 3‑stage wizard. Routes are defined in `routes/web.php` and
handled by `App\Http\Controllers\InstallController`.

| Stage | URL | What happens |
|-------|-----|--------------|
| **1. Welcome** | `/install` | Intro screen; click through to start. |
| **2. Database** | `/install/database` | Enter **DB host**, **DB name**, **DB user**, **DB password**. On submit, the installer: tests the connection, runs `migrate:fresh` on that database, writes the credentials into `.env`, and sets `DB_CONNECTION=mysql` + `SESSION_DRIVER=database`. |
| **3. Admin** | `/install/admin` | Enter **username**, **email**, and **password** (min 6 chars, confirmed). Creates the admin user (`is_admin = 1`), logs you in, and **locks the installer** by creating `storage/installed`. |
| **Done** | redirect to `/dashboard` | "Setup complete!" — you are now signed in as admin. |

**Important behaviors:**

- ⚠️ **Stage 2 runs `migrate:fresh`** — it **drops and recreates all tables** in
  the target database. Always point the installer at an **empty** database.
- The **`CheckInstallation`** middleware redirects every request to `/install`
  until the lock file exists *and* the `users` table is present.
- The **`InstallerLock`** middleware blocks the installer (redirects to
  `/login`) once `storage/installed` exists, so the wizard cannot be re‑run by
  accident.
- During installation the session driver is temporarily switched to `file` so
  the wizard keeps working while the database/session tables are being built.

> **Fields & defaults for XAMPP:** Host `127.0.0.1`, Database `laravel_posts`,
> User `root`, Password *(leave blank)*.

---

## 9. Serving the application

This project includes a root‑level `index.php` shim and an `.htaccess` so it can
be served from a sub‑folder **without exposing `/public`** in the URL. There are
three common ways to run it.

### Option 1 — PHP built‑in dev server (simplest)
```bash
php artisan serve
```
Then open **http://127.0.0.1:8000** (you'll be redirected to `/install` on first
run). This always serves from `public/`, so URLs are clean.

> Convenience: `composer run dev` starts the PHP server, queue listener, log
> tailer (Pail), and Vite dev server together via `concurrently`.

### Option 2 — Apache, project in a sub‑folder (zero‑config)
Drop the project anywhere under the web root — e.g.
`C:\xampp\htdocs\Laravel-Posts` (or `…/stories` on a server) — and browse to:
```
http://localhost/Laravel-Posts/install
```
The bundled root `.htaccess` + `index.php` shim route this automatically and
keep `/public` out of the URL. **No `RewriteBase` editing is required** — the
`.htaccess` no longer hardcodes a base, so it adapts to whatever folder name (or
the domain root) you serve it from. You do **not** need `/public` in the address.

> Requirements: Apache `mod_rewrite` must be enabled and the directory must allow
> `.htaccess` overrides (`AllowOverride All`). Both are on by default in XAMPP.
> If clean URLs 404, see the `mod_rewrite` row in [Troubleshooting](#13-troubleshooting).

### Option 3 — Apache Virtual Host pointing at `public/` (recommended for prod)
Point a vhost `DocumentRoot` directly at the project's **`public/`** directory —
this is the standard, most secure Laravel setup.

```apache
<VirtualHost *:80>
    ServerName laravel-posts.local
    DocumentRoot "/var/www/stories/public"
    <Directory "/var/www/stories/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
Add the host to DNS (or your `hosts` file), restart Apache, and visit
**http://laravel-posts.local/install**.

> Whichever option you choose, set **`APP_URL`** in `.env` to the URL you use so
> generated links and assets resolve correctly.

---

## 10. Environment (.env) configuration reference

Key variables (full list in `.env.example`). For this app the database section
is the part you must get right:

```dotenv
APP_NAME=Laravel-Posts
APP_ENV=local
APP_KEY=                     # filled by `php artisan key:generate`
APP_DEBUG=true               # set to false in production
APP_URL=http://localhost     # set to your actual URL (see Section 9)

# --- Database (REQUIRED: use mysql, not sqlite) ---
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_posts
DB_USERNAME=root
DB_PASSWORD=                 # blank by default on XAMPP

# These default to the database; left as-is they require the DB to be migrated
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log              # emails are written to the log by default
```

> **Note:** `.env.example` ships with `DB_CONNECTION=sqlite`. The web installer
> overwrites this to `mysql` automatically. If you install **manually**, change
> it to `mysql` yourself before running `php artisan migrate`.

After editing `.env` manually, refresh cached config:
```bash
php artisan config:clear
```

### Optional production optimizations
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Remember to clear these (`php artisan optimize:clear`) after future `.env`
changes.

---

## 11. Verifying the installation

1. **Health check:** visit `/up` — Laravel's built‑in health endpoint should
   return a success page.
2. **Database:** open phpMyAdmin and confirm the tables exist: `users`, `posts`,
   `post_media`, `comments`, `roles`, `permissions`, `role_user`,
   `permission_role`, `settings`, `subprofiles`, `audit_logs`, `activity_logs`,
   `sessions`, `cache`, `jobs`, plus others.
3. **Login:** go to `/login` and sign in with the admin account you created in
   the installer.
4. **Dashboard:** you should land on `/dashboard` and be able to create a post.
5. **Uploads:** create a post with an image (or set a profile avatar) and
   confirm the image displays — this verifies `storage:link` is working.

---

## 12. Re‑running / resetting the installer

The installer is intentionally one‑time. To run it again (e.g. on a clean
environment) you must remove the lock and clear the database:

```bash
# 1. Remove the installer lock file
del storage\installed            # Windows
# rm storage/installed           # macOS/Linux

# 2. (Optional) wipe the schema so Stage 2 starts clean
php artisan migrate:fresh

# 3. Clear cached config/sessions
php artisan optimize:clear
```
Then browse to `/install` again.

> ⚠️ `migrate:fresh` **deletes all data**. Only do this on a development or
> throwaway database.

---

## 13. Troubleshooting

| Symptom | Cause / Fix |
|---------|-------------|
| `Could not open input file: artisan` (Method C) | You didn't `cd ..` after `mv * ..`; `artisan` is in the parent folder. Step back up, then re‑run the artisan commands. |
| Browser keeps redirecting to **/install** | The lock file `storage/installed` is missing or the `users` table doesn't exist. Complete the wizard, or create the lock file manually (see Method B). |
| **"Database connection failed"** in installer | MySQL isn't running, or wrong host/user/password. Start MySQL and verify credentials. On XAMPP the user is `root` with a **blank** password. |
| **"Installer is already locked. Please login."** | `storage/installed` exists. This is normal after setup — go to `/login`. To reinstall, see [Section 12](#12-re-running--resetting-the-installer). |
| Uploaded **images are broken** (404) | The storage symlink is missing. Run `php artisan storage:link`. On hosts without symlink support, copy `storage/app/public` into `public/storage`. |
| **`npm run build` fails** with an esbuild/rollup platform error | The bundled `node_modules` came from another OS. Delete it and run `npm install` again on this server, then `npm run build`. |
| **Full‑text search error** / migration fails on `fulltext` | You're not on MySQL/MariaDB, or the `posts` table isn't InnoDB. This app requires MySQL/MariaDB; SQLite/Postgres won't get the full‑text index. |
| `Vite manifest not found` | Front‑end assets weren't built. Run `npm install` then `npm run build` (or `npm run dev` while developing). |
| **`composer install` fails** on PHP version | You're on PHP < 8.2. Install/enable PHP 8.2+ and ensure the CLI uses it (`php -v`). |
| **500 error / blank page** after editing `.env` | Stale cached config. Run `php artisan config:clear` (and `optimize:clear`). |
| Clean URLs 404 under Apache (`/install` not found, but `/public/install` works) | `mod_rewrite` is disabled or the folder has `AllowOverride None`, so `.htaccess` is ignored. Enable `mod_rewrite` and set `AllowOverride All` for the directory, then restart Apache. The shipped `.htaccess` needs **no** `RewriteBase` — leave it unset so it works in any folder. |
| **`Permission denied`** writing `.env`, `storage/`, or `bootstrap/cache` | The web server user must own/write these. Run `chown -R <web-user>:<web-user> .` (e.g. `www-data`, `apache`, or `daemon`) and `chmod -R 775 storage bootstrap/cache`. |
| `.env` secrets readable by other users | `chmod -R 755 .` exposed `.env`. Run `chmod 600 .env`. |
| Logs to inspect | Application log: `storage/logs/laravel.log`. The installer logs each step there (look for `--- Installation process started ---`). |

---

### Quick reference — manual install (XAMPP / dev)

```bash
cd C:\xampp\htdocs\Laravel-Posts
composer install
npm install            # requires Node.js 20.19+ (or 22.12+)
copy .env.example .env
php artisan key:generate
# edit .env -> DB_CONNECTION=mysql, DB_DATABASE=laravel_posts, DB_USERNAME=root, DB_PASSWORD=
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
# open http://127.0.0.1:8000/install  (or /login if you locked the installer)
```

### Quick reference — server install (release tarball)

```bash
mkdir -p stories && cd stories
wget https://github.com/AdarshAshwani/Laravel-Posts/archive/refs/tags/v1.00.010.tar.gz
tar -xvzf v1.00.010.tar.gz
cd Laravel-Posts-1.00.010
mv * ..
mv .editorconfig .env .env.example .gitattributes .gitignore .htaccess .version ..
cd ..
rm -rf Laravel-Posts-1.00.010 v1.00.010.tar.gz
php artisan key:generate
php artisan storage:link
npm install && npm run build      # server needs Node.js 20.19+ (or 22.12+)
chmod -R 755 . && chmod 600 .env
chown -R daemon:daemon .          # use your web-server user
# point the vhost/.htaccess at this folder, then open /install in a browser
```

---

*Generated for Postify v1.00.010. If you change the database engine,
serving path, or hosting environment, revisit Sections 2, 9, and 10.*
