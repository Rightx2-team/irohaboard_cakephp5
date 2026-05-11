# [iroha Board — CakePHP 5 / PHP 8.5 Edition].(https://rightx2-team.github.io/irohaboard_cakephp5/)

**Version 5.0.0**

A modernized fork of [iroha Board](https://irohaboard.irohasoft.jp/), an open-source LMS (Learning Management System) originally built with CakePHP 2.x and PHP 7.x.

---

## What's Changed

| | Original | This Fork (v5.0.0) |
|---|---|---|
| Framework | CakePHP 2.x | CakePHP 5.x |
| PHP | 7.x | 8.5.1 |
| Language | Japanese only | Japanese / English |
| Authentication | Local DB only | Local DB + AD/LDAP |
| Docker | Not supported | Supported |

### Key Improvements

- **Full migration from CakePHP 2 to CakePHP 5**
  - Updated MVC architecture (Table/Entity model, new routing, middleware)
  - Replaced deprecated APIs with CakePHP 5 equivalents
  - Authentication rewritten using `cakephp/authentication` plugin

- **PHP 8.5.1 compatibility**
  - Resolved all deprecation warnings and type errors
  - Modernized code throughout controllers, models, and templates

- **Bilingual support (Japanese / English)**
  - A language switcher button is displayed in the header on every page.
  - Users can toggle between Japanese and English at any time without logging out.
  - The selected language is stored in the session and persists across pages.
  - All UI labels, buttons, and messages are fully translated via CakePHP i18n (`.po` files).
  - Database content (course names, questions, etc.) is not translated — only the UI is.

- **Active Directory / LDAP authentication**
  - Each user account can independently use either local DB authentication or AD/LDAP authentication.
  - The authentication method is configured per user in the admin screen (User Edit → Authentication Method).
  - For AD users, passwords are verified directly against the Active Directory server — no password is stored in the local database.
  - Falls back gracefully: if LDAP is unavailable, local DB accounts remain accessible.
  - Supports both UPN format (`user@domain.local`) and NetBIOS format (`DOMAIN\user`) automatically.
  - Primary and secondary domain controllers are tried in order for high availability.

- **Docker support**
  - Single command startup with Docker Compose
  - PHP 8.4 + Apache + MariaDB containers
  - Separate configurations for local development and production

---

## Quick Start with Docker (Recommended)

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/) installed

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git
cd YOUR_REPO

# 2. Create your environment file
cp .env.example .env

# 3. Edit .env with your preferred settings
#    At minimum, change SECURITY_SALT and DB_PASSWORD
#    (The defaults work for local development)

# 4. Build and start containers
docker compose up -d --build

# 5. Open in browser
#    http://localhost/
```

### First-time database setup

After the containers start, import the database schema:

```bash
docker compose exec db mariadb -u irohaboard -p irohaboard < config/sql/schema.sql
```

### Useful Docker commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View application logs
docker compose logs -f app

# Access the application container shell
docker compose exec app bash

# Access the database
docker compose exec db mariadb -u irohaboard -p
```

### Local development

For local development, `docker-compose.override.yml` is automatically loaded, which:
- Enables debug mode (`APP_DEBUG=true`)
- Mounts source directories for live code changes without rebuilding
- Exposes the database port to your host machine (default: 3306)

### Production deployment

For production, do **not** use `docker-compose.override.yml`:

```bash
docker compose -f docker-compose.yml up -d --build
```

Set secure values in `.env`:

```env
APP_DEBUG=false
SECURITY_SALT=<long random string>
DB_PASSWORD=<strong password>
DB_ROOT_PASSWORD=<strong password>
```

---

## Manual Installation (without Docker)

### Requirements

- PHP 8.1 or higher (tested on 8.5.1)
- CakePHP 5.x
- MySQL / MariaDB
- Apache with `mod_rewrite` enabled
- `php-ldap` extension (required for AD/LDAP authentication)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git
cd YOUR_REPO

# 2. Install dependencies
composer install

# 3. Configure database
cp config/app_local.php.example config/app_local.php
# Edit config/app_local.php with your database credentials

# 4. Import the database schema
mysql -u YOUR_USER -p YOUR_DATABASE < config/sql/schema.sql
```

### Upgrading an existing installation

If you are upgrading from the original iroha Board (CakePHP 2), apply the migration:

```bash
mysql -u YOUR_USER -p YOUR_DATABASE < config/sql/migrations/add_auth_type.sql
```

### Configuring AD/LDAP authentication

To enable AD authentication for a user:

1. Log in to the admin panel
2. Go to **User Management → Edit User**
3. Set **Authentication Method** to `AD / LDAP`
4. Save — the user can now log in with their AD password

To configure the AD connection, edit `src/Identifier/LdapIdentifier.php`:

```php
protected array $_defaultConfig = [
    'ldapServers' => ['192.168.1.1', '192.168.1.2'], // Primary, Secondary DC
    'ldapPort'    => 389,
    'ldapDomain'  => 'example.local',
    'baseDn'      => 'DC=example,DC=local',
    ...
];
```

> **Note:** `php-ldap` extension must be enabled in `php.ini`.

---

## Changelog

### v5.0.0
- Full migration from CakePHP 2.x to CakePHP 5.x
- PHP 8.5 compatibility
- Bilingual UI support (Japanese / English)
- Active Directory / LDAP authentication (per-user)
- Docker support

---

## Original Project

- **iroha Board** by iroha Soft Co., Ltd.
- Repository: https://github.com/irohasoft/irohaboard
- License: GPL-3.0

## License

GPL-3.0 — see LICENSE for details.
