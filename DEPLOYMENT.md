# Deployment and Production Readiness

This document covers deployment for Summit Split, a PHP application with `public/` as its web root.

## Current readiness

The application is suitable for local development and a staging environment. It is **not production-ready yet** because SQLite is currently used as a local single-file database and demo accounts are seeded automatically. Complete the production blockers below before exposing the application to real users.

## Requirements

- PHP 8.2 or newer
- PHP extensions: `session`, `openssl`, and `mbstring`
- Apache with `mod_rewrite` or Nginx with PHP-FPM
- HTTPS with a valid certificate
- A process supervisor for PHP-FPM

The web server must point to the `public/` directory, never the repository root. This prevents `src/` and `tests/` from being served as web files.

## Local deployment

From the repository root:

```sh
php -S 127.0.0.1:8088 -t public
```

Run the checks before testing the browser:

```sh
php -l src/functions.php
php tests/tests.php
```

Open `http://127.0.0.1:8088/` after the server starts.

## Linux host deployment

1. Install PHP, PHP-FPM, and the web server using the host package manager.
2. Deploy the repository outside the web server's default public directory, for example `/var/www/summit-split`.
3. Set the web root to `/var/www/summit-split/public`.
4. Make the application readable by the web server user. Do not make the repository writable by PHP.
5. Run the syntax and ranking checks from the release directory:

```sh
php -l src/functions.php
php tests/tests.php
```

6. Configure HTTPS and redirect HTTP traffic to HTTPS.
7. Restart or reload PHP-FPM and the web server.
8. Run the smoke tests below against the deployed hostname.

## Nginx and PHP-FPM example

Replace the hostname and PHP-FPM socket with values for the host:

```nginx
server {
    listen 80;
    server_name summit.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name summit.example.com;
    root /var/www/summit-split/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\. {
        deny all;
    }
}
```

The application currently uses explicit PHP page links, so a rewrite fallback is not required for the existing pages. Keep `try_files` in place for safe handling of future routes.

## Production blockers

Complete these before go-live:

- Move users and password hashes to a managed production database or identity provider. Disable demo account seeding and never keep demo passwords in source control.
- Load credentials and environment-specific settings from environment variables or a secret manager.
- Add CSRF protection to every state-changing form.
- Add login rate limiting and audit logging for failed and successful logins.
- Configure secure session cookies: `Secure`, `HttpOnly`, and `SameSite=Lax` or stricter.
- Set `display_errors=Off` and log errors privately with `log_errors=On`.
- Replace the generic `Forbidden` response with a branded error page that does not expose internals.
- Add a backup and restore procedure if route or account data becomes persistent.
- Pin and self-host frontend assets if external font availability is not acceptable for the deployment.
- Add an automated deployment pipeline that runs syntax checks and tests before release.

## Release checklist

- [ ] Production credentials are stored outside the repository.
- [ ] Demo accounts and passwords are removed.
- [ ] Database migrations and backups are tested, if applicable.
- [ ] `public/` is the only configured document root.
- [ ] HTTPS is active and HTTP redirects to HTTPS.
- [ ] PHP error display is disabled.
- [ ] Session cookie flags are configured.
- [ ] CSRF protection and login rate limiting are enabled.
- [ ] `php -l src/functions.php` passes.
- [ ] `php tests/tests.php` passes.
- [ ] Public page returns `200`.
- [ ] Guest access to `admin.php` redirects to `login.php`.
- [ ] Invalid credentials do not create a session.
- [ ] A normal user receives `403` for `admin.php`.
- [ ] An admin can access `admin.php`.
- [ ] Route ordering and estimated-time labels are visible in the deployed UI.

## Smoke tests

Replace the hostname before running these commands:

```sh
curl -fsS -o /dev/null -w '%{http_code}\n' https://summit.example.com/
curl -fsS -o /dev/null -w '%{http_code} %{redirect_url}\n' https://summit.example.com/admin.php
```

Expected results are `200` for the public page and `302` to `login.php` for unauthenticated admin access. Test authenticated flows with a temporary cookie jar and never put real passwords in shell history or CI logs.