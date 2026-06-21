# Contact Database Setup Guide

This README explains how to set up the contact form database and related configuration for the Portfolio site.

> Repo: ayvaaan/Portfolio
> Description: this is my portoflio i made by using java, html, css, database , sql, phh

Table of contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [File Structure](#file-structure)
- [Database schema](#database-schema)
- [Configuration](#configuration)
- [Security notes](#security-notes)
- [Troubleshooting](#troubleshooting)
- [Advanced configuration](#advanced-configuration)
- [Support](#support)

---

## Prerequisites

- PHP 7.0 or higher
- MySQL or MariaDB server
- A web server with PHP support (Apache, Nginx, etc.)
- Access to the repository files (this project places database-related assets in `assets/`)

## Quick start

1. Create the database:

```sql
CREATE DATABASE portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the schema (from the project):

```bash
mysql -u root -p portfolio_db < assets/db/schema.sql
```

3. Update your database credentials in `assets/php/config.php` (see Configuration below).

4. Start the web server and submit the contact form (e.g., `contact.html`) to verify messages are stored.

## File structure

```
assets/
├── db/
│   └── schema.sql              # Database schema for contact messages
├── php/
│   ├── config.php              # Database and admin configuration
│   ├── submit-contact.php      # Form submission handler
│   └── get-messages.php        # Message retrieval / admin API
└── js/
    └── scripts.js              # Client-side scripts
```

## Database schema

Recommended tables:

- contacts
  - id (INT AUTO_INCREMENT PRIMARY KEY)
  - name (VARCHAR)
  - email (VARCHAR)
  - message (TEXT)
  - created_at (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
  - status (ENUM: 'unread','read')

- admin_users (optional)
  - id (INT AUTO_INCREMENT PRIMARY KEY)
  - username (VARCHAR)
  - password_hash (VARCHAR)
  - created_at (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)

(Use the concrete `assets/db/schema.sql` file for the exact SQL statements.)

## Configuration

Edit `assets/php/config.php` and set your credentials:

```php
define('DB_HOST', 'localhost');      // database host
define('DB_USER', 'root');           // database username
define('DB_PASS', '');               // database password
define('DB_NAME', 'portfolio_db');   // database name

// Admin credentials - DO NOT keep defaults in production
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'your_secure_password');
```

Important: replace `ADMIN_PASSWORD` with a secure value and prefer storing credentials outside version control or using environment variables.

## Security notes

Follow these production best practices:

- Use HTTPS in production to protect credentials and form submissions.
- Use server-side authentication for the messages page (sessions, server-checked credentials).
- Store admin passwords hashed using `password_hash()` and verify with `password_verify()`.
- Use prepared statements (PDO or mysqli with prepared statements) to prevent SQL injection.
- Sanitize user input for output with `htmlspecialchars()` to avoid XSS.
- Protect sensitive PHP files and configuration from public access (web server rules or `.htaccess`).

Example `.htaccess` snippet to restrict direct access when using Apache:

```apache
# Deny access to raw PHP config or handlers from the web if needed
<FilesMatch "^(config|submit-contact|get-messages)\.php$">
  Require all granted
</FilesMatch>
```

Note: adjust the rules above to match your hosting and security requirements. The example simply shows where to add rules; in many setups you'd deny or restrict access to config files.

## Troubleshooting

- "Database connection failed": ensure MySQL/MariaDB is running and credentials in `config.php` are correct.
- "Table doesn't exist": run `assets/db/schema.sql` against the correct database.
- Form submissions not saving: check PHP error logs, file permissions, and database access.
- Messages not showing on dashboard: verify admin credentials and check browser console for JS errors; ensure `get-messages.php` is reachable.

## Advanced configuration

- Email notifications: send an email after successful insert in `submit-contact.php`:

```php
// After successful insertion
mail('your-email@example.com', 'New Contact Message', "Name: $name\nEmail: $email\nMessage: $message");
```

- Scheduled backups (cron example):

```bash
0 2 * * * mysqldump -u root -p'password' portfolio_db > /backup/portfolio_$(date +\%Y\%m\%d).sql
```

- Export messages to CSV: add a server-side route that queries messages and streams CSV output with proper headers.

## Support

References and useful links:

- PHP Documentation: https://www.php.net/docs.php
- MySQL Documentation: https://dev.mysql.com/doc/
- MySQL Security: https://dev.mysql.com/doc/refman/latest/en/security.html

---

Last updated: 2026-06-21
