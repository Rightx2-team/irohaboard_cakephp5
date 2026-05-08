# iroha Board — CakePHP 5 / PHP 8.5 Edition

A modernized fork of [iroha Board](https://irohaboard.irohasoft.jp/), an open-source LMS (Learning Management System) originally built with CakePHP 2.x and PHP 7.x.

---

## What's Changed

| | Original | This Fork |
|---|---|---|
| Framework | CakePHP 2.x | CakePHP 5.x |
| PHP | 7.x | 8.5.1 |
| Language | Japanese only | Japanese / English |
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
  - Language switcher button in the header (available on all pages)
  - User preference stored in session
  - Full UI translation via CakePHP i18n (`.po` files)

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
docker compose exec db mariadb -u irohaboard -p irohaboard < path/to/schema.sql
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
mysql -u YOUR_USER -p YOUR_DATABASE < path/to/schema.sql
```

---

## Original Project

- **iroha Board** by iroha Soft Co., Ltd.
- Repository: https://github.com/irohasoft/irohaboard
- License: GPL-3.0

## License

GPL-3.0 — see LICENSE for details.
