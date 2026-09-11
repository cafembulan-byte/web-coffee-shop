# Summit Split

Summit Split is a small HTML and PHP route comparison site for Mount Alder. It ranks routes by estimated time to the summit, adjusts estimates for hiking pace, and shows difficulty and terrain as supporting context.

## Run locally

From the project directory:

```sh
php -S 127.0.0.1:8088 -t public
```

Open `http://127.0.0.1:8088` in a browser.

## Demo accounts

- Hiker: `hiker@example.com` / `summit-user`
- Admin: `admin@example.com` / `summit-admin`

The admin page is protected by the `admin` session role. Passwords are verified with `password_verify()` against hashes created with `password_hash()`.

## Checks

```sh
php tests/tests.php
```

The ranking implementation lives in `src/functions.php`; route times sort ascending, with difficulty, elevation gain, and distance as tie-breakers.

## Structure

```text
public/      Browser-facing PHP pages
src/         Route ranking and session helpers
style.css    Main stylesheet
tests/       Focused PHP checks
```

See [DEPLOYMENT.md](DEPLOYMENT.md) for production deployment steps and the go-live checklist.

For cPanel Rumahweb, follow [CPANEL_RUMAHWEB.md](CPANEL_RUMAHWEB.md).

Member accounts use SQLite in `database/summit.sqlite`. The database is created automatically from `database/schema.sql` on first request, and new members can register at `/register.php`.
# web-coffee-shop
asa
