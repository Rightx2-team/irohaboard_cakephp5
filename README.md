# iroha Board — CakePHP 5 / PHP 8.5 Edition

A modernized fork of [iroha Board](https://irohaboard.irohasoft.jp/), an open-source LMS (Learning Management System) originally built with CakePHP 2.x and PHP 7.x.

---

## What's Changed

| | Original | This Fork |
|---|---|---|
| Framework | CakePHP 2.x | CakePHP 5.x |
| PHP | 7.x | 8.5.1 |
| Language | Japanese only | Japanese / English |

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

---

## Requirements

- PHP 8.1 or higher (tested on 8.5.1)
- CakePHP 5.x
- MySQL / MariaDB
- Apache with `mod_rewrite` enabled

## Installation

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

## Original Project

- **iroha Board** by iroha Soft Co., Ltd.
- Repository: https://github.com/irohasoft/irohaboard
- License: GPL-3.0

## License

GPL-3.0 — see LICENSE for details.