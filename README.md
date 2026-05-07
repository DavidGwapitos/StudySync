# StudySync - Student Task Manager

A modern, responsive web application for students to organize their academic tasks and assignments. Built with PHP, MySQL, and Bootstrap 5.

## 🌟 Features

- **User Authentication**: Secure registration and login with password hashing
- **Task Management**: Create, read, update, and delete tasks
- **Task Organization**: Assign tasks to subjects with priority levels
- **Deadline Tracking**: Set due dates and track overdue tasks
- **Progress Monitoring**: View completion statistics and progress tracking
- **Task Filtering**: Filter tasks by subject, priority, and status
- **Search Functionality**: Search tasks by title or description
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices
- **Session Management**: Secure session handling with timeout protection
- **Data Validation**: Comprehensive input validation and sanitization
- **SQL Injection Prevention**: Uses prepared statements for all database queries

## 📋 Prerequisites

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache, Nginx, or similar with PHP support
- **Bootstrap 5**: Included via CDN
- **Bootstrap Icons**: Included via CDN

## 🚀 Installation

### Step 1: Set Up the Database

1. Open your MySQL client (PHPMyAdmin or command line)
2. Execute the SQL script to create the database:
   ```sql
   -- Copy and execute the contents of database/database.sql
   ```
3. Or import the SQL file directly:
   ```bash
   mysql -u root -p < database/database.sql
   ```

### Step 2: Configure Database Connection

1. Open `config/Database.php`
2. Update the database credentials:
   ```php
   private $host = 'localhost';        // Your MySQL host
   private $db_name = 'studysync';     // Your database name
   private $db_user = 'root';          // Your MySQL username
   private $db_password = '';          // Your MySQL password
   ```

### Step 3: Deploy to Web Server

1. Copy the entire StudySync folder to your web server's document root:
   - Apache: `/var/www/html/`
   - Nginx: `/usr/share/nginx/html/`
   - Local: `C:/xampp/htdocs/` (Windows with XAMPP)

2. Ensure proper file permissions:
   ```bash
   chmod -R 755 /path/to/StudySync
   chmod -R 777 /path/to/StudySync/assets/
   ```

### Step 4: Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/StudySync/`
3. Register a new account or use the test account:
   - **Email**: john@example.com
   - **Password**: password123

## 📁 Project Structure

```
StudySync/
├── config/              # Configuration files
│   ├── Config.php       # Global configuration and helper functions
│   └── Database.php     # Database connection class
├── controllers/         # Business logic controllers
│   ├── AuthController.php      # Authentication logic
│   ├── TaskController.php      # Task management logic
│   └── DashboardController.php # Dashboard logic
├── models/             # Database models
│   ├── User.php        # User model with authentication methods
│   └── Task.php        # Task model with CRUD operations
├── views/              # View templates
│   └── tasks/          # Task-related views
│       ├── view.php    # View all tasks
│       ├── create.php  # Create new task
│       └── edit.php    # Edit existing task
├── assets/             # Static assets
│   ├── css/
│   │   └── style.css   # Custom stylesheet
│   └── js/
│       └── script.js   # Client-side JavaScript
├── api/                # API endpoints for AJAX
│   └── task/
│       ├── update-status.php
│       └── delete.php
├── database/
│   └── database.sql    # Database schema
├── index.php           # Landing page
├── login.php           # Login page
├── register.php        # Registration page
├── dashboard.php       # Main dashboard
├── profile.php         # User profile
└── logout.php          # Logout handler
```

## 🔒 Security Features

- **Password Hashing**: bcrypt algorithm with cost factor of 10
- **Prepared Statements**: All database queries use prepared statements
- **Input Validation**: All user inputs are validated
- **Input Sanitization**: All inputs are sanitized using htmlspecialchars
- **Session Management**: Session timeout after 1 hour of inactivity
- **CSRF Protection**: Session-based security measures

## 🎨 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tasks Table
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    subject VARCHAR(100) NOT NULL,
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    due_date DATETIME,
    status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 📝 API Endpoints

### Authentication
- `POST /login.php` - User login
- `POST /register.php` - User registration
- `GET /logout.php` - User logout

### Task Management
- `POST /api/task/update-status.php` - Update task status
- `POST /api/task/delete.php` - Delete a task

## 🎯 Available Subjects

- Mathematics
- English
- Science
- History
- Geography
- Physics
- Chemistry
- Biology
- Computer Science
- Economics
- Business Studies
- Literature
- Art
- Physical Education
- Other

## 🎨 Priority Levels

- **Low**: Can be completed anytime
- **Medium**: Should be completed soon
- **High**: Urgent, needs immediate attention

## 📊 Dashboard Statistics

The dashboard displays:
- **Total Tasks**: Count of all tasks
- **Completed Tasks**: Count of finished tasks
- **Pending Tasks**: Count of tasks waiting to be done
- **Overdue Tasks**: Count of tasks past their due date
- **Overall Progress**: Completion percentage
- **Upcoming Tasks**: Tasks due within 7 days
- **Recent Tasks**: Latest tasks created

## 🛠️ Configuration Options

Edit `config/Config.php` to customize:

```php
define('APP_NAME', 'StudySync');           // Application name
define('BASE_URL', 'http://localhost/StudySync/'); // Base URL
define('SESSION_TIMEOUT', 3600);           // Session timeout (seconds)
define('PASSWORD_ALGORITHM', PASSWORD_BCRYPT); // Password algorithm
define('ITEMS_PER_PAGE', 10);             // Tasks per page
```

## 🔧 Troubleshooting

### Issue: Database Connection Failed
- Check database credentials in `config/Database.php`
- Ensure MySQL server is running
- Verify database exists and is accessible

### Issue: Blank Pages
- Check PHP error log
- Enable error reporting in `config/Config.php`
- Ensure all required files exist

### Issue: Session Not Working
- Check if PHP session directory is writable
- Verify PHP session settings in php.ini
- Clear browser cookies and try again

### Issue: File Upload Problems
- Check folder permissions (must be 755 or 777)
- Ensure uploads directory exists
- Check disk space availability

## 💡 Usage Tips

1. **Create Tasks Systematically**: Group related tasks by subject
2. **Set Realistic Deadlines**: Plan ahead and avoid last-minute rushes
3. **Use Priority Levels**: Mark urgent tasks as high priority
4. **Review Regularly**: Check your dashboard daily
5. **Complete Tasks**: Mark tasks as complete once finished
6. **Filter Effectively**: Use filters to focus on specific task types

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📄 License

This project is provided as-is for educational purposes.

## 🤝 Contributing

To contribute improvements:
1. Make your changes
2. Test thoroughly
3. Follow the existing code style
4. Submit your improvements

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Review the code comments
3. Check browser console for errors
4. Review PHP error logs

## 🎓 Learning Resources

This project demonstrates:
- MVC architecture patterns
- PHP object-oriented programming
- MySQL database design
- Secure authentication
- AJAX for dynamic updates
- Bootstrap 5 responsive design
- JavaScript DOM manipulation
- RESTful API concepts

## ✨ Future Enhancements

Potential features for future versions:
- Email notifications for due tasks
- Task categories and tags
- Recurring tasks
- Task attachments
- Collaboration features
- Mobile app version
- Dark mode theme
- Advanced analytics
- Export to calendar
- Backup and restore

## 🔐 Important Security Notes

1. **Change Database Credentials**: Update default credentials immediately
2. **Use HTTPS**: Deploy on HTTPS in production
3. **Regular Backups**: Back up your database regularly
4. **Update Dependencies**: Keep PHP and MySQL updated
5. **Monitor Logs**: Check error logs for suspicious activity

## 📊 Version History

- **v1.0.0** (2024): Initial release
  - User authentication
  - Task CRUD operations
  - Dashboard with statistics
  - Task filtering and search
  - Responsive design

---

**Last Updated**: May 2024
**Developed for**: Educational purposes
**Tested on**: PHP 7.4+, MySQL 5.7+, Modern Browsers
