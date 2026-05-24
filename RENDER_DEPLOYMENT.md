# Render Production Deployment

Use these settings on Render for AppTrack Pro.

## Required Environment Variables

Set these in the Render service environment:

```env
APP_NAME=AppTrack Pro
APP_ENV=production
APP_DEBUG=false
APP_URL=https://YOUR-RENDER-SERVICE.onrender.com
APP_TIMEZONE=Africa/Lagos
TRUSTED_PROXIES=*

DB_CONNECTION=pgsql
DB_HOST=YOUR_SUPABASE_POOLER_OR_DB_HOST
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=YOUR_SUPABASE_DB_USER
DB_PASSWORD=YOUR_SUPABASE_DB_PASSWORD
DB_SSLMODE=require

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=null

CACHE_STORE=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

Do not add Supabase anon keys or Supabase auth settings. Laravel handles authentication, authorization, sessions, validation, and business logic.

## Docker Deploy

The included `Dockerfile` installs PHP dependencies, installs Node dependencies, runs `npm run build`, removes `public/hot`, runs migrations, seeds roles/demo users, and starts Laravel on Render's `$PORT`.

After deploying, open:

```text
https://YOUR-RENDER-SERVICE.onrender.com/login
```

The login page should load compiled assets from `/build/...`, not from a Vite dev server. If the page looks unstyled, redeploy after confirming `public/hot` is not present in the deployed container.
