1z1 Consent
===========

A privacy-first, open-source consent management platform.

Demo (Not always up)
--------------------

 * [Live Demo (Frontend)](https://www.1z1-consent.un-zero-un.net)
 * [Live Demo (Back-office)](https://1z1-consent.un-zero-un.net/admin)

Features
--------
 * User-friendly interface for managing consent preferences 
   ![Screenshot](./tests/e2e/consent-dialog.spec.ts-snapshots/Consent-dialog-box-It-checks-trackers-manually-1-chromium-linux.png)
 * Saas-Like (multi-site, multi-client and multi-tenant)
 * Highly configurable, customizable and extensible
 * DNT support (optional)
 * Consent logging and reporting
 * Page view tracking
 * Multiple RDBMS support (PostgreSQL, MariaDB and SQLite tested, MySQL, Oracle and SQLServer should work)
 * Configurable cache layer (Redis, Memcached, APCu)
 * GDPR Registry Auto-generation

Usage
-----

### Single Container run

```bash
 $ docker run -it --rm -p 80:80 -p 443:443 --name 1z1-consent \
       -e SERVER_NAME="localhost, www.localhost" \
       -e MAIN_DOMAIN="localhost" \
       -e APP_SECRET="$(shell openssl rand -hex 32)" \
       ghcr.io/un-zero-un/1z1-consent:latest
```

This will start a single container with both the web server and the database (SQLite) running.
Data will be stored in the container, so if you remove the container, you will lose all your data.
You can also use a volume to persist the data:

```bash
 $ docker run -it --rm -p 80:80 -p 443:443 --name 1z1-consent \
       -e SERVER_NAME="localhost, www.localhost" \
       -e MAIN_DOMAIN="localhost" \
       -e APP_SECRET="$(shell openssl rand -hex 32)" \
       -v 1z1-consent-data:/app/var/data \
       ghcr.io/un-zero-un/1z1-consent:latest
```

You can use an external database by providing the `DATABASE_URL` environment variable:
`mysql://user:password@host:port/dbname?serverVersion=8&charset=utf8mb4` or 
`postgres://user:password@host:port/dbname?serverVersion=17&charset=utf8`

### Docker Compose

A complete docker compose config is provided. The simplest way to use it is via Makefile:

```bash
 $ make run
```

If you have `docker compose` and `mkcert` available, it'll run the stack with a local CA signed certificate for 
`localhost` and `www.localhost`, a postgres database and a APCu cache in development mode. 

### Configuration

The application is configured via environment variables. Available variables are :


| Variable Name                  | Description                                             | Default Value                                   |
|--------------------------------|---------------------------------------------------------|-------------------------------------------------|
| APP_ENV                        | The application environment (dev, prod, test)           | prod                                            |
| MAIN_DOMAIN                    | The main domain used for Back-office and script serving | localhost                                       |
| SERVER_NAME                    | Comma separated list of server names (domains)          | localhost, www.localhost                        |
| APP_SECRET                     | A secret key used for encryption and hashing            | !ChangeMe!                                      |
| MAILER_DSN                     | The DSN used for sending emails                         | smtp://smtp:1025                                |
| DATABASE_URL                   | The database connection URL                             | sqlite:///%kernel.project_dir%/var/data/data.db |
| CACHE_DSN                      | The cache connection URL                                | apcu://default                                  |
| AUTHORIZED_ADMIN_EMAIL_DOMAINS | Authorized email domains for admin (Google Login)       |                                                 |
| GOOGLE_CLIENT_ID               | Google OAuth Client ID for admin login                  |                                                 |
| GOOGLE_CLIENT_SECRET           | Google OAuth Client Secret for admin login              |                                                 |
| EMAIL_FROM_ADDRESS             | The email address used as the sender                    | no-reply@localhost                              |
| DNT_ENABLED                    | Enable Do Not Track support                             | 1                                               |
