# Contact Database Setup Guide

This guide will help you set up a database system to store contact form submissions on your portfolio website.

## Prerequisites

- PHP 7.0 or higher
- MySQL or MariaDB database server
- A web server supporting PHP (Apache, Nginx, etc.)

## Setup Instructions

### Step 1: Create the Database

1. Open your MySQL/MariaDB admin tool (phpMyAdmin, MySQL Workbench, or command line)
2. Create a new database named `portfolio_db`:
   ```sql
   CREATE DATABASE portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Run the SQL schema from `assets/db/schema.sql`:
   ```sql
   USE portfolio_db;
   -- Copy and paste the contents of assets/db/schema.sql here
   ```

Or if using command line:
```bash
mysql -u root -p portfolio_db < assets/db/schema.sql
```

### Step 2: Configure Database Credentials

1. Open `assets/php/config.php`
2. Update the following variables with your actual database credentials:
   ```php
   define('DB_HOST', 'localhost');      // Your database host
   define('DB_USER', 'root');           // Your database username
   define('DB_PASS', '');               // Your database password
   define('DB_NAME', 'portfolio_db');   // Your database name
   ```

3. Change the admin credentials (used for viewing messages):
   ```php
   define('ADMIN_USERNAME', 'admin');
   define('ADMIN_PASSWORD', 'your_secure_password'); // Change this!
   ```

### Step 3: Test the Setup

1. Make sure your web server is running with PHP support
2. Test the contact form by visiting your website and submitting a message
3. Check if the message appears in the database

### Step 4: View Contact Messages

1. Visit `http://your-website.com/messages.html`
2. Log in with your admin credentials (default: admin / your_secure_password)
3. View, mark as read, or delete contact messages

## File Structure

```
assets/
├── db/
│   └── schema.sql              # Database schema
├── php/
│   ├── config.php              # Database configuration
│   ├── submit-contact.php      # Form submission handler
│   └── get-messages.php        # Message retrieval API
└── js/
    └── scripts.js              # Updated with database integration
```

## Features

### Contact Form (`contact.html`)
- Name, Email, and Message fields
- Client-side and server-side validation
- Messages are stored in the database with timestamps
- Automatic "unread" status for new messages

### Message Dashboard (`messages.html`)
- Admin login page
- View all contact messages
- Statistics (total messages, unread count)
- Mark messages as read
- Delete messages
- Responsive design with dark/light theme support

### Database Schema

#### `contacts` table
- `id`: Auto-incrementing message ID
- `name`: Sender's name
- `email`: Sender's email address
- `message`: Message content
- `created_at`: Timestamp of submission
- `status`: Message status (unread/read)

#### `admin_users` table (Optional)
- `id`: User ID
- `username`: Admin username
- `password_hash`: Hashed password
- `created_at`: User creation timestamp

## Security Notes

⚠️ **Important Security Considerations:**

1. **Change the admin password** in `config.php` immediately
2. **Use HTTPS** for your website in production
3. **Implement proper authentication** for the messages page:
   - The current authentication is basic and client-side only
   - Use sessions and cookies for production
   - Hash passwords using `password_hash()` and `password_verify()`

4. **Sanitize all inputs** - The code already includes:
   - HTML escaping with `htmlspecialchars()`
   - SQL injection protection with prepared statements
   - Email validation with `filter_var()`

5. **Protect sensitive files**:
   - Add `.htaccess` file to the `assets/php/` directory:
     ```apache
     <FilesMatch "^(config|submit-contact|get-messages)\.php$">
         Order allow,deny
         Allow from all
     </FilesMatch>
     ```

6. **Database backups** - Regularly backup your `portfolio_db` database

## PHP Functions Used

### `submit-contact.php`
- Validates form input (name, email, message)
- Sanitizes data with `htmlspecialchars()`
- Uses prepared statements to prevent SQL injection
- Returns JSON response

### `get-messages.php`
- Retrieves all messages from database
- Supports marking messages as read
- Supports deleting messages
- Returns statistics (total, unread counts)

## Troubleshooting

### "Database connection failed"
- Check if MySQL/MariaDB server is running
- Verify database credentials in `config.php`
- Ensure the database `portfolio_db` exists

### "Table doesn't exist"
- Run the schema.sql file again
- Verify you're using the correct database

### Form submissions not saving
- Check PHP error logs
- Verify file permissions on `assets/php/` directory
- Ensure PHP can write to the database

### Messages not showing on dashboard
- Verify admin credentials
- Check browser console for JavaScript errors
- Ensure `get-messages.php` is accessible

## Advanced Configuration

### Email Notifications
You can add email notifications when a new message is received:
```php
// Add to submit-contact.php after successful insertion
mail('your-email@example.com', 'New Contact Message', "Name: $name\nEmail: $email\nMessage: $message");
```

### Scheduled Backups
Set up a cron job to automatically backup your database:
```bash
0 2 * * * mysqldump -u root -p'password' portfolio_db > /backup/portfolio_$(date +\%Y\%m\%d).sql
```

### Export to CSV
Add a feature to export messages as CSV for record-keeping or analysis.

## Support

For issues or questions, refer to:
- PHP Documentation: https://www.php.net/docs.php
- MySQL Documentation: https://dev.mysql.com/doc/
- MySQL Security: https://dev.mysql.com/doc/refman/latest/en/security.html

---

Last Updated: 2026-06-20
