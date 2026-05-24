# AppTrack Pro
### Application Support Team Activity Tracking System
*Built for Npontu Technologies — Platforms Developer Assessment*

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [What the System Does](#2-what-the-system-does)
3. [Technology Stack](#3-technology-stack)
4. [Project Structure](#4-project-structure)
5. [Prerequisites](#5-prerequisites)
6. [Step-by-Step Local Setup](#6-step-by-step-local-setup)
7. [Supabase Database Setup](#7-supabase-database-setup)
8. [Seeded Test Accounts](#8-seeded-test-accounts)
9. [Roles & Permissions](#9-roles--permissions)
10. [Key Features Walkthrough](#10-key-features-walkthrough)
11. [Architecture Decisions](#11-architecture-decisions)
12. [Security Implementation](#12-security-implementation)
13. [Deployment Guide (Render / Railway)](#13-deployment-guide)
14. [Troubleshooting](#14-troubleshooting)

---

## 1. Project Overview

**AppTrack Pro** is a professional, enterprise-grade web application that allows an Applications Support Team to:

- Log and track daily operational activities (e.g., "Daily SMS Count Comparison with Logs")
- Update the status of each activity (`Pending`, `In Progress`, `Done`, `Escalated`)
- Automatically capture who made each status change, when, and from where (IP, browser)
- Provide a **Shift Handover Dashboard** to clearly see all pending activities before shift change
- Run custom reports over any date range and export to PDF or Excel
- Manage users with role-based access control

> **All authentication, authorization, sessions, and business logic are handled 100% inside Laravel. Supabase is used only as the hosted PostgreSQL database provider.**

---

## 2. What the System Does

### Requirement Mapping

| Requirement | How it's fulfilled |
|---|---|
| Input activities (e.g. SMS Count) | `Activity` model with `activity_type` field and predefined types |
| Update status (Done/Pending) + Remark | `ActivityController@updateStatus` → `ActivityService` |
| Capture personnel bio + timestamp | `ActivityUpdate` model stores name, role, department, IP, browser, timestamp |
| View all activities per day with updates | Dashboard + Activities index with date filter |
| Shift handover view of pending activities | Dashboard "Shift Handover Required" alert block |
| Reporting with custom date ranges | `ReportController` with PDF + Excel export |
| User authentication | Laravel Breeze-style auth, sessions, CSRF, password hashing |
| Role-based access | Spatie Laravel Permission: admin, supervisor, support_staff |

---

## 3. Technology Stack

| Layer | Technology |
|---|---|
| Backend Framework | **Laravel 11** |
| Frontend Views | **Laravel Blade + Alpine.js** |
| Reactive Components | **Laravel Livewire 3** |
| CSS Framework | **Tailwind CSS 3** |
| Charts | **ApexCharts** |
| Database | **PostgreSQL via Supabase** |
| Authentication | **Laravel Sessions + Bcrypt** |
| Authorization | **Spatie Laravel Permission** |
| PDF Export | **barryvdh/laravel-dompdf** |
| Excel Export | **maatwebsite/laravel-excel** |
| Build Tool | **Vite** |

---

## 4. Project Structure

```
activitytracker/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php  ← Login/Logout + audit log
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   └── NewPasswordController.php
│   │   │   ├── DashboardController.php    ← Main dashboard stats + handover
│   │   │   ├── ActivityController.php     ← CRUD + status updates
│   │   │   ├── ReportController.php       ← Filters, PDF, Excel export
│   │   │   ├── UserController.php         ← Admin user management
│   │   │   ├── NotificationController.php ← In-app notifications
│   │   │   └── ProfileController.php      ← User profile + password change
│   │   └── Middleware/
│   │       └── TrackUserActivity.php
│   │
│   ├── Models/
│   │   ├── User.php             ← UUID, soft deletes, HasRoles, relationships
│   │   ├── Activity.php         ← Core activity model + scopes + accessors
│   │   ├── ActivityUpdate.php   ← Per-update snapshot (who, when, IP, browser)
│   │   ├── AuditLog.php         ← Full system audit trail
│   │   └── AppNotification.php  ← In-app notification model
│   │
│   ├── Policies/
│   │   └── ActivityPolicy.php   ← Row-level access: who can update which activity
│   │
│   ├── Services/
│   │   ├── ActivityService.php  ← Business logic: create, status update, notify, stats
│   │   └── AuditService.php     ← Centralized audit logging
│   │
│   ├── Exports/
│   │   └── ActivitiesExport.php ← Excel export with styled header row
│   │
│   └── Providers/
│       └── AppServiceProvider.php ← Policy registration, Paginator, Gates
│
├── database/
│   ├── migrations/              ← All table definitions with indexes + foreign keys
│   └── seeders/
│       └── DatabaseSeeder.php   ← Roles, permissions, 4 test users
│
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php        ← Main layout: sidebar, header, flash messages
│   │   └── auth.blade.php       ← Auth page layout (login, forgot password)
│   ├── auth/                    ← Login, forgot-password, reset-password
│   ├── dashboard/index.blade.php← Stats, handover alert, chart, recent updates
│   ├── activities/              ← index, create, edit, show (with timeline)
│   ├── reports/                 ← index (filters + export), pdf, audit-logs
│   ├── admin/users/             ← index, create, edit
│   ├── notifications/index.blade.php
│   ├── profile/edit.blade.php
│   └── components/pagination.blade.php
│
└── routes/
    ├── web.php                  ← All routes with auth + role middleware
    └── auth.php                 ← Login/logout/password reset routes
```

---

## 5. Prerequisites

Make sure the following are installed on your machine:

| Tool | Version | Check |
|---|---|---|
| **PHP** | 8.2 or higher | `php --version` |
| **Composer** | 2.x | `composer --version` |
| **Node.js** | 18+ | `node --version` |
| **npm** | 9+ | `npm --version` |
| **Git** | Any | `git --version` |

You also need a **Supabase account** (free tier works perfectly).

---

## 6. Step-by-Step Local Setup

### Step 1 — Clone / Download the project

```bash
# If using Git:
git clone <your-repo-url> apptrack-pro
cd apptrack-pro

# Or just extract the zip into a folder and open a terminal inside it.
```

### Step 2 — Install PHP dependencies

```bash
composer install
```

> If you see errors about extensions, make sure PHP has these enabled in `php.ini`:
> `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`

### Step 3 — Install Node dependencies

```bash
npm install
```

### Step 4 — Copy environment file

```bash
cp .env.example .env
```

### Step 5 — Generate application key

```bash
php artisan key:generate
```

> This writes `APP_KEY=base64:...` into your `.env`. **Never share this key.**

### Step 6 — Configure your `.env` file

Open `.env` in any text editor. The most important settings to update:

```env
APP_NAME="AppTrack Pro"
APP_URL=http://localhost:8000

# ← Update these 3 lines with your Supabase details (see Section 7)
DB_HOST=db.YOUR_PROJECT_REF.supabase.co
DB_PASSWORD=YOUR_SUPABASE_PASSWORD
```

Everything else can stay as-is for local development.

### Step 7 — Run database migrations

```bash
php artisan migrate
```

You should see a list of tables being created. If you get a connection error, double-check your Supabase credentials in `.env`.

### Step 8 — Seed the database

```bash
php artisan db:seed
```

This creates:
- 3 roles: `admin`, `supervisor`, `support_staff`
- 9 permissions and assigns them to roles
- 4 test users (see Section 8 for login details)

### Step 9 — Build frontend assets

```bash
# For development (with hot reload):
npm run dev

# For production build:
npm run build
```

### Step 10 — Start the development server

Open a **second terminal** (keep `npm run dev` running in the first) and run:

```bash
php artisan serve
```

Then open your browser at: **http://localhost:8000**

Log in with: `admin@apptrack.pro` / `Admin@123`

---

## 7. Supabase Database Setup

Supabase provides a free hosted PostgreSQL database. Here's how to get your credentials:

### Step 1 — Create a Supabase project

1. Go to [supabase.com](https://supabase.com) and sign up (free)
2. Click **"New Project"**
3. Give it a name (e.g. `apptrack-pro`)
4. Set a strong **Database Password** — save this, you'll need it
5. Choose your region (pick the closest to Ghana/Africa)
6. Click **"Create new project"** and wait ~2 minutes

### Step 2 — Get your connection details

1. In your project dashboard, go to **Settings → Database**
2. Scroll down to **"Connection parameters"**
3. Copy these values:

```
Host:     db.xxxxxxxxxxxx.supabase.co   ← your DB_HOST
Database: postgres                       ← your DB_DATABASE
Port:     5432                           ← your DB_PORT
User:     postgres                       ← your DB_USERNAME
Password: (the one you set above)        ← your DB_PASSWORD
```

### Step 3 — Update your `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=YourPasswordHere
DB_SSLMODE=require
```

### Step 4 — Verify connection

```bash
php artisan tinker
# Then type:
DB::connection()->getPdo();
# You should see: PDO { ... } — this means it's connected!
```

> **Important:** Supabase requires SSL. The `DB_SSLMODE=require` line handles this.
> Do NOT enable Row Level Security (RLS) on any table — Laravel handles all access control.

---

## 8. Seeded Test Accounts

After running `php artisan db:seed`, these accounts are ready to use:

| Role | Name | Email | Password |
|---|---|---|---|
| **Admin** | System Administrator | admin@apptrack.pro | `Admin@123` |
| **Supervisor** | Kwame Mensah | supervisor@apptrack.pro | `Super@123` |
| **Support Staff** | Ama Boateng | ama.boateng@apptrack.pro | `Staff@123` |
| **Support Staff** | Kofi Asante | kofi.asante@apptrack.pro | `Staff@123` |

---

## 9. Roles & Permissions

### Permission Matrix

| Action | Admin | Supervisor | Support Staff |
|---|:---:|:---:|:---:|
| View activities | ✅ | ✅ | ✅ |
| Create activities | ✅ | ✅ | ✅ |
| Edit any activity | ✅ | ✅ | ❌ |
| Edit own/assigned activity | ✅ | ✅ | ✅ |
| Update status | ✅ | ✅ | ✅ (own/assigned) |
| Delete activities | ✅ | ❌ | ❌ |
| View reports | ✅ | ✅ | ❌ |
| Export PDF/Excel | ✅ | ✅ | ❌ |
| View audit logs | ✅ | ✅ | ❌ |
| Manage users | ✅ | ❌ | ❌ |

### How Authorization Works

All authorization is in **Laravel Policies** (`app/Policies/ActivityPolicy.php`) and **Spatie roles**.

```
User logs in → Laravel session created
  ↓
Request hits route → `auth` middleware checks session
  ↓
`role:admin` middleware → checks Spatie role table
  ↓
Policy check (e.g. `can('update', $activity)`) → ActivityPolicy evaluates row-level access
```

There is **zero Supabase RLS** involved. Laravel is the sole enforcer.

---

## 10. Key Features Walkthrough

### Dashboard

The home page shows at a glance:
- **7 stat cards**: total, pending, in-progress, done, escalated, critical, overdue
- **Shift Handover Alert** (red, pulsing): appears when there are pending activities from previous days — the most important feature for shift handovers
- **7-day trend chart** using ApexCharts (bar chart of total/done/pending per day)
- **Today's update timeline** on the right
- **Today's activities table** with status indicators

### Activity Detail & Update History

`/activities/{id}` shows:
- Full activity info with colour-coded priority stripe
- Complete **update history timeline** — every status change logged with:
  - The name, role, and department of the person who made the change
  - The exact date and time
  - IP address and browser
  - Previous status → New status with visual arrows
  - Remarks entered

### Shift Handover

The red alert block at the top of the Dashboard automatically shows:
- Any activity from **previous days** that is still not `done`
- Who last handled it and when
- Priority labels so supervisors know what's critical

### Reports

`/reports` lets you filter by:
- Date range (from/to)
- Status, Priority, Personnel, Activity Type, Free-text search

Then export as:
- **PDF** (formatted, landscape A4 via DomPDF)
- **Excel** (styled header, all columns via Laravel Excel)
- **Print** (browser print dialog)

### Audit Logs

`/reports/audit-logs` (admin/supervisor only) shows a full trail of:
- Every login and logout with IP address
- Every activity created
- Every status change
- Every export
- Every user created/modified

---

## 11. Architecture Decisions

### Service Layer Pattern

Business logic lives in `app/Services/`, not in controllers. This keeps controllers thin:

```php
// Controller just delegates:
$activity = $this->activityService->createActivity($validated);

// Service handles: creating the record, audit logging, and notifications
```

### UUID Primary Keys

All main tables use UUIDs instead of auto-increment integers. This:
- Prevents ID enumeration attacks
- Is safer for a multi-server deployment
- Matches Supabase's typical patterns

### Soft Deletes

`User` and `Activity` models use soft deletes (`deleted_at`). Records are never truly removed — just hidden. Admins can restore them if needed.

### Activity Update Snapshot

Every time someone updates a status, a full snapshot is stored in `activity_updates`:
- We store the **name** and **role** at the time of update (not just a foreign key), so the record remains accurate even if the user's role changes later.

---

## 12. Security Implementation

| Threat | Laravel Countermeasure |
|---|---|
| CSRF attacks | `@csrf` token on every form, verified by `VerifyCsrfToken` middleware |
| SQL injection | Eloquent ORM + prepared statements — no raw SQL |
| XSS | Blade `{{ }}` auto-escapes output |
| Unauthorized access | `auth` middleware on all routes; `ActivityPolicy` for row-level |
| Password storage | `bcrypt` via Laravel's `Hash::make()` |
| Session fixation | `$request->session()->regenerate()` on login |
| Brute force | Failed login attempts are audit-logged; rate limiting via `throttle:6,1` can be added to auth routes |
| Privilege escalation | Spatie roles checked on every sensitive action; `role:admin` middleware on admin routes |
| Data exposure | Soft deletes + non-sequential UUIDs prevent record enumeration |

---

## 13. Deployment Guide

### Option A: Render (Recommended — Free Tier Available)

1. **Push your code to GitHub**

2. **Create a new Web Service on [render.com](https://render.com)**:
   - Build Command: `composer install --no-dev && npm install && npm run build && php artisan migrate --force && php artisan db:seed --force`
   - Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

3. **Set Environment Variables** in Render dashboard:
   ```
   APP_KEY=           (run php artisan key:generate --show locally to get it)
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=           (your render.com URL)
   DB_CONNECTION=pgsql
   DB_HOST=           (Supabase host)
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=       (Supabase password)
   DB_SSLMODE=require
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```

### Option B: Railway

1. Connect your GitHub repo to [railway.app](https://railway.app)
2. Add the same environment variables as above
3. Railway auto-detects Laravel and runs the right build commands

### Option C: Shared Hosting / VPS

```bash
# On the server:
git clone <repo> /var/www/apptrack
cd /var/www/apptrack
composer install --no-dev --optimize-autoloader
npm install && npm run build
cp .env.example .env
# Edit .env with your production values
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

Point your web server (Nginx/Apache) `document root` to `/var/www/apptrack/public`.

#### Sample Nginx config:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/apptrack/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

---

## 14. Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
php artisan optimize:clear
```

### "SQLSTATE[08006]" (PostgreSQL connection error)
- Check your `.env` DB_ values match Supabase exactly
- Make sure `DB_SSLMODE=require` is set
- Try connecting with `psql "host=... dbname=postgres user=postgres password=..."` to verify

### "Permission table not found" after migrating
```bash
php artisan migrate:fresh --seed
```
This drops and recreates all tables. **Only use on a fresh database.**

### Blank page / 500 error
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
# Then check:
tail -50 storage/logs/laravel.log
```

### CSS not loading (missing Tailwind styles)
```bash
npm run build
# or for dev:
npm run dev
```

### Activities not showing on dashboard
The dashboard defaults to **today's date**. If you seeded activities with a past date, use the Activities page with the date filter to find them.

---

## Quick Reference Commands

```bash
# Start dev server
php artisan serve

# Watch and recompile assets
npm run dev

# Re-run migrations from scratch (⚠ deletes all data)
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# View recent logs
tail -f storage/logs/laravel.log

# Open interactive console
php artisan tinker

# List all registered routes
php artisan route:list
```

---

*AppTrack Pro — Built with Laravel 11 for Npontu Technologies Platforms Developer Assessment*
*© 2025 — All security and access control handled by Laravel. Supabase used as PostgreSQL provider only.*
