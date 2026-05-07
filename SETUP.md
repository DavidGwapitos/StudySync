# StudySync - Setup Guide

Complete step-by-step guide to set up StudySync on your system.

## Quick Start (5 Minutes)

### Prerequisites
- XAMPP, WAMP, LAMP, or similar with PHP 7.4+ and MySQL
- Web browser
- Text editor (optional)

### Steps

#### 1. Extract Files
```bash
# Windows (XAMPP example)
Extract StudySync to C:\xampp\htdocs\

# Linux/Mac
Extract StudySync to /var/www/html/ or /usr/share/nginx/html/
```

#### 2. Create Database
```bash
# Option A: Using PHPMyAdmin
1. Open http://localhost/phpmyadmin
2. Go to "SQL" tab
3. Copy contents of database/database.sql
4. Paste and execute

# Option B: Using MySQL Command Line
mysql -u root -p < database/database.sql

# Option C: Using MySQL Workbench
1. Open MySQL Workbench
2. Open database/database.sql
3. Execute
```

#### 3. Configure Database Connection
1. Open `config/Database.php`
2. Update credentials (if different from default):
   ```php
   private $host = 'localhost';
   private $db_name = 'studysync';
   private $db_user = 'root';
   private $db_password = '';
   ```

#### 4. Start Web Server
```bash
# XAMPP
Start Apache and MySQL from XAMPP Control Panel

# Manual (Linux/Mac)
sudo systemctl start apache2
sudo systemctl start mysql
```

#### 5. Access Application
- Open browser: `http://localhost/StudySync/`
- Login with test account:
  - Email: `john@example.com`
  - Password: `password123`

---

## Detailed Setup Instructions

### System Requirements

**Minimum:**
- PHP 7.4
- MySQL 5.7
- 50 MB disk space

**Recommended:**
- PHP 8.0+
- MySQL 8.0+
- 100 MB disk space
- 2 GB RAM

### Installation on Different Platforms

#### Windows (XAMPP)

1. **Install XAMPP**
   - Download from https://www.apachefriends.org/
   - Run installer
   - Choose installation folder (default: C:\xampp)

2. **Extract StudySync**
   - Extract to `C:\xampp\htdocs\StudySync`

3. **Start XAMPP**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

4. **Create Database**
   - Open http://localhost/phpmyadmin
   - Click "SQL" tab at top
   - Paste contents of `database/database.sql`
   - Click "Go"

5. **Update Configuration**
   - Open `config/Database.php`
   - Check MySQL credentials

6. **Access Application**
   - Navigate to `http://localhost/StudySync/`

#### Linux (Apache)

1. **Install Requirements**
   ```bash
   sudo apt-get update
   sudo apt-get install apache2 php php-mysql mysql-server
   ```

2. **Enable PHP Module**
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

3. **Extract StudySync**
   ```bash
   sudo cp -r StudySync /var/www/html/
   sudo chown -R www-data:www-data /var/www/html/StudySync
   ```

4. **Create Database**
   ```bash
   mysql -u root -p < StudySync/database/database.sql
   ```

5. **Update Configuration**
   - Edit `config/Database.php`
   - Set correct credentials

6. **Fix Permissions**
   ```bash
   chmod -R 755 /var/www/html/StudySync
   chmod -R 777 /var/www/html/StudySync/logs
   ```

7. **Access Application**
   - Navigate to `http://your-server-ip/StudySync/`

#### Mac (MAMP/Homebrew)

1. **Install MAMP**
   - Download from https://www.mamp.info/
   - Install with default settings

2. **Extract StudySync**
   - Extract to `/Applications/MAMP/htdocs/StudySync`

3. **Create Database**
   - Open PHPMyAdmin from MAMP
   - Execute SQL from `database/database.sql`

4. **Update Configuration**
   - Edit `config/Database.php`
   - Default port might be 3306 or custom

5. **Start Services**
   - Open MAMP
   - Click "Start"

6. **Access Application**
   - Navigate to `http://localhost:8888/StudySync/` (MAMP default)

### Configuration

#### Database Configuration
```php
// config/Database.php
class Database {
    private $host = 'localhost';        // MySQL host
    private $db_name = 'studysync';     // Database name
    private $db_user = 'root';          // MySQL user
    private $db_password = '';          // MySQL password
}
```

#### Application Configuration
```php
// config/Config.php
define('BASE_URL', 'http://localhost/StudySync/');  // Your URL
define('SESSION_TIMEOUT', 3600);                     // 1 hour
define('ITEMS_PER_PAGE', 10);                        // Tasks per page
```

### Verification Checklist

After setup, verify:

- [ ] Database created successfully
- [ ] All files extracted in correct location
- [ ] Apache/PHP running
- [ ] MySQL running
- [ ] Can access index.php
- [ ] Can login with test account
- [ ] Can create new task
- [ ] Can view dashboard
- [ ] CSS and JavaScript loaded

### Testing the Installation

1. **Test Login**
   - Email: `john@example.com`
   - Password: `password123`
   - Should redirect to dashboard

2. **Test Registration**
   - Click "Sign Up"
   - Create new account
   - Should show success message

3. **Test Task Creation**
   - Login
   - Click "New Task"
   - Fill form and submit
   - Task should appear in list

4. **Test Filters**
   - Go to "My Tasks"
   - Use filters
   - Verify filtering works

### Troubleshooting

#### Cannot Connect to Database
```php
// Check credentials in config/Database.php
// Test connection manually in MySQL client
mysql -h localhost -u root -p -D studysync
```

#### Blank Pages
- Check PHP error log
- Enable error reporting in `config/Config.php`
- Check browser console for errors

#### 404 Errors
- Verify .htaccess exists
- Check Apache mod_rewrite is enabled
- Verify file paths in links

#### Permission Denied
```bash
# Fix permissions (Linux/Mac)
chmod -R 755 /path/to/StudySync
chmod -R 777 /path/to/StudySync/logs
```

#### Session Not Working
- Clear browser cookies
- Check PHP session directory exists
- Verify PHP session settings in php.ini

### Production Deployment

Before deploying to production:

1. **Change Database Credentials**
   ```php
   // Use strong, unique credentials
   private $db_user = 'studysync_user';
   private $db_password = 'YourStrongPassword123!';
   ```

2. **Update Base URL**
   ```php
   define('BASE_URL', 'https://yourdomain.com/StudySync/');
   ```

3. **Enable HTTPS**
   - Get SSL certificate
   - Update BASE_URL to use https://

4. **Disable Debug Mode**
   ```php
   ini_set('display_errors', 0);
   ini_set('log_errors', 1);
   ```

5. **Set Proper Permissions**
   ```bash
   chmod -R 755 /path/to/StudySync
   chmod -R 700 /path/to/StudySync/config
   chmod -R 777 /path/to/StudySync/logs
   ```

6. **Backup Database**
   ```bash
   mysqldump -u root -p studysync > backup.sql
   ```

7. **Monitor Logs**
   - Check error logs regularly
   - Monitor security logs

### First Login Credentials

**Test Account:**
- Email: `john@example.com`
- Password: `password123`

**Create Your Own Account:**
1. Click "Sign Up"
2. Enter your details
3. Click "Create Account"
4. Login with new credentials

### Getting Help

If you encounter issues:

1. **Check Logs**
   - PHP error log: `logs/error.log`
   - MySQL error log: Check MySQL configuration

2. **Read README.md**
   - Comprehensive documentation
   - Features overview
   - API endpoints

3. **Review Code Comments**
   - Each file has detailed comments
   - Function descriptions provided

4. **Browser Console**
   - Press F12 to open developer tools
   - Check Console tab for JavaScript errors
   - Check Network tab for failed requests

### Next Steps

After successful setup:

1. **Create Test Data**
   - Create multiple tasks
   - Assign to different subjects
   - Set various priorities

2. **Test Features**
   - Mark tasks complete
   - Filter by subject
   - Search by keywords

3. **Explore Dashboard**
   - View statistics
   - Check upcoming tasks
   - Review progress

4. **Customize**
   - Update subjects in `config/Config.php`
   - Modify colors in `assets/css/style.css`
   - Adjust timeouts as needed

---

**Setup Complete!** 🎉

You're now ready to use StudySync. Happy organizing!

For detailed usage instructions, see README.md
