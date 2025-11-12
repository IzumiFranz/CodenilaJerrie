# 🎓 Quiz & Learning Management System (LMS)

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![OpenAI](https://img.shields.io/badge/OpenAI-API-412991?style=for-the-badge&logo=openai&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)

**A comprehensive, AI-powered Learning Management System built with Laravel 11**

[Features](#-features) • [Installation](#-installation) • [Documentation](#-documentation) • [Demo](#-demo) • [License](#-license)

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [Security](#-security)
- [License](#-license)
- [Support](#-support)

---

## 🌟 Overview

Quiz LMS is a modern, feature-rich Learning Management System designed for educational institutions. It provides a complete solution for managing courses, students, instructors, and assessments with AI-powered features for automatic quiz generation and grading.

### Key Highlights

- 🤖 **AI-Powered**: Automatic question generation and validation using OpenAI GPT-4
- ⚡ **Real-time**: Live search, filters, and notifications with Livewire
- 📱 **Responsive**: Mobile-first design with Bootstrap 5
- 🔒 **Secure**: Role-based access control, audit logging, CSRF protection
- 📊 **Analytics**: Comprehensive performance tracking and reporting
- 📧 **Email Integration**: Automated notifications via Gmail SMTP
- 🎯 **User-Friendly**: Intuitive interface for all user roles

---

## ✨ Features

### 👨‍💼 Admin Module

- **User Management**
  - Create/edit users (Admin, Instructor, Student)
  - Bulk user upload via CSV
  - User status management (active/inactive/suspended)
  - Soft delete with trash management
  - Auto-generate usernames and passwords

- **Academic Management**
  - Course management (CRUD operations)
  - Subject management with specializations
  - Section management with capacity limits
  - Student enrollment (single & bulk)
  - Instructor assignments to subjects/sections

- **Content Oversight**
  - View all lessons created by instructors
  - View all quizzes and results
  - Delete inappropriate content
  - Monitor quiz performance analytics

- **System Management**
  - Feedback management with response system
  - Audit log tracking (all user activities)
  - Bulk notification system
  - System health monitoring
  - CSV export functionality

### 👨‍🏫 Instructor Module

- **Lesson Management**
  - Create lessons with rich text editor
  - Upload files (PDF, DOC, PPT, etc.)
  - Publish/unpublish lessons
  - View lesson analytics

- **Question Bank**
  - Create questions (Multiple Choice, True/False, Identification, Essay)
  - AI-powered question generation from lesson content
  - Question validation using AI
  - Question difficulty analysis
  - Question tagging and categorization
  - Duplicate question detection

- **Quiz Management**
  - Create quizzes with custom settings
  - Add questions from question bank
  - Configure time limits and passing scores
  - Set quiz availability windows
  - Randomize questions and choices
  - Enable/disable answer review

- **Assessment & Grading**
  - Auto-grade MC and True/False questions
  - Manual grading for essay questions
  - AI-assisted essay grading (optional)
  - View student attempts and scores
  - Export quiz results to CSV
  - Performance analytics dashboard

- **Student Progress Tracking**
  - View individual student performance
  - Section performance comparison
  - Identify struggling students
  - Export progress reports

### 👨‍🎓 Student Module

- **Learning**
  - View enrolled subjects and sections
  - Access published lessons
  - Download lesson materials
  - Track lesson completion

- **Quizzes**
  - View available quizzes
  - Take quizzes with timer
  - Auto-save answers
  - View results and feedback
  - Review correct answers (if enabled)
  - Track quiz history and attempts

- **Profile & Feedback**
  - Update personal information
  - Change password
  - Upload profile picture
  - Submit feedback on lessons/quizzes
  - View notifications

### 🤖 AI Features

- **Question Generation**
  - Generate questions from lesson content
  - Support for multiple question types
  - Configurable difficulty levels
  - Bloom's taxonomy classification

- **Question Validation**
  - Grammar and clarity checking
  - Quality scoring (0-100)
  - Distractor effectiveness analysis
  - Improvement suggestions

- **Analytics**
  - Difficulty index calculation
  - Discrimination index analysis
  - Question performance tracking
  - Auto-retirement of poor questions

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0+
- **Queue**: Laravel Queue (Database/Redis)
- **Cache**: Redis (optional)

### Frontend
- **CSS Framework**: Bootstrap 5.3
- **Admin Template**: SB Admin 2
- **JavaScript**: Vanilla JS + jQuery
- **Real-time**: Livewire 3.x
- **Icons**: Font Awesome 6

### APIs & Services
- **AI**: OpenAI API (GPT-4)
- **Email**: Gmail SMTP
- **Storage**: Local/AWS S3 (configurable)

### Development Tools
- **Version Control**: Git
- **Dependency Management**: Composer, NPM
- **Code Quality**: PHP CS Fixer, Laravel Pint

---

## 💻 System Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ / MariaDB 10.3+ / PostgreSQL 13+
- **Web Server**: Apache 2.4+ / Nginx 1.18+
- **RAM**: 2GB minimum
- **Storage**: 5GB minimum
- **Composer**: 2.5+
- **Node.js**: 18+ (for asset compilation)

### Recommended Requirements
- **PHP**: 8.3
- **Database**: MySQL 8.0+ with InnoDB engine
- **Web Server**: Nginx with PHP-FPM
- **RAM**: 4GB or more
- **Storage**: 20GB SSD
- **Redis**: For caching and sessions

### PHP Extensions Required
```
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD or Imagick
```

---

## 🚀 Installation

### Quick Installation (5 minutes)

```bash
# 1. Clone the repository
git clone https://github.com/rozuragon/quiz-lms.git
cd quiz-lms

# 2. Install PHP dependencies
composer install

# 3. Install NPM dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your .env file
nano .env
# Set DB_DATABASE, DB_USERNAME, DB_PASSWORD
# Set OPENAI_API_KEY
# Set MAIL_* variables

# 7. Create database
mysql -u root -p
CREATE DATABASE quizlms;
exit;

# 8. Run migrations and seeders
php artisan migrate --seed

# 9. Link storage
php artisan storage:link

# 10. Build assets
npm run build

# 11. Start development server
php artisan serve
```

Visit: `http://localhost:8000`

### Default Login Credentials

After running seeders, use:
```
Username: admin
Password: password
```

**⚠️ IMPORTANT**: Change default password immediately after first login!

---

## ⚙️ Configuration

### 1. Database Setup

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quizlms
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. OpenAI API Configuration

Get your API key from: https://platform.openai.com/api-keys

```env
OPENAI_API_KEY=sk-your-key-here
OPENAI_MODEL=gpt-4
OPENAI_MAX_TOKENS=2000
OPENAI_TEMPERATURE=0.7
```

**Cost Estimation**:
- Question generation: ~$0.01 per question
- Question validation: ~$0.005 per question
- Essay grading: ~$0.02 per essay

### 3. Email Configuration (Gmail)

1. Enable 2-Step Verification in Google Account
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Configure:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Quiz LMS"
```

### 4. File Upload Settings

```env
MAX_LESSON_FILE_SIZE=10240        # 10MB in KB
MAX_AVATAR_FILE_SIZE=2048         # 2MB in KB
ALLOWED_LESSON_FILE_TYPES=pdf,doc,docx,ppt,pptx,txt
```

### 5. Quiz Default Settings

```env
QUIZ_DEFAULT_TIME_LIMIT=60        # minutes
QUIZ_DEFAULT_PASSING_SCORE=60     # percentage
QUIZ_DEFAULT_MAX_ATTEMPTS=3
```

### 6. Cache & Session (Optional but Recommended)

For better performance, install Redis:

```bash
# Install Redis
sudo apt-get install redis-server

# Configure .env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 📖 Usage

### For Administrators

1. **Login** as admin: `http://localhost:8000/login`
2. **Dashboard** shows system overview
3. **Create Users**:
   - Single: Users → Add User
   - Bulk: Users → Bulk Upload (download CSV template)
4. **Setup Academic Structure**:
   - Create Courses
   - Create Subjects
   - Create Sections
   - Create Specializations
5. **Enroll Students**:
   - Enrollments → Enroll Student
   - Or use Bulk Enrollment
6. **Assign Instructors**:
   - Assignments → Create Assignment
   - Match instructor specialization to subject
7. **Monitor System**:
   - View Audit Logs
   - Respond to Feedback
   - Send Notifications

### For Instructors

1. **Login** with instructor credentials
2. **Create Lessons**:
   - Lessons → Create Lesson
   - Add content and upload files
   - Publish when ready
3. **Build Question Bank**:
   - Question Bank → Create Question
   - Or use AI to generate questions
   - Validate questions with AI
4. **Create Quizzes**:
   - Quizzes → Create Quiz
   - Add questions from question bank
   - Configure settings (time, attempts, etc.)
   - Publish quiz
5. **Grade & Review**:
   - View quiz results
   - Grade essay questions manually
   - Provide feedback to students
6. **Track Progress**:
   - Student Progress → View performance
   - Identify students needing help
   - Export progress reports

### For Students

1. **Login** with student credentials
2. **View Dashboard** showing enrolled subjects
3. **Study Lessons**:
   - Click on subject
   - View lessons
   - Download materials
4. **Take Quizzes**:
   - View available quizzes
   - Start quiz attempt
   - Answers auto-save
   - Submit when complete
5. **Review Results**:
   - View score and feedback
   - Review correct answers (if enabled)
   - Track progress
6. **Submit Feedback**:
   - Rate and comment on lessons/quizzes
   - Help improve content

---

## 📚 API Documentation

### Authentication

All API requests require authentication via Laravel Sanctum tokens.

```bash
# Get token
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}

# Use token in subsequent requests
Authorization: Bearer {token}
```

### Endpoints

#### Users
```
GET    /api/users                    # List users
GET    /api/users/{id}               # Get user details
POST   /api/users                    # Create user
PUT    /api/users/{id}               # Update user
DELETE /api/users/{id}               # Delete user
```

#### Courses
```
GET    /api/courses                  # List courses
GET    /api/courses/{id}             # Get course details
GET    /api/courses/{id}/subjects    # Get course subjects
```

#### Quizzes
```
GET    /api/quizzes                  # List available quizzes
GET    /api/quizzes/{id}             # Get quiz details
POST   /api/quizzes/{id}/start       # Start quiz attempt
POST   /api/quiz-attempts/{id}/submit # Submit quiz
GET    /api/quiz-attempts/{id}/results # Get results
```

For complete API documentation, run:
```bash
php artisan route:list --path=api
```

---

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/UserManagementTest.php
```

### Test Structure

```
tests/
├── Feature/
│   ├── Admin/
│   │   ├── UserManagementTest.php
│   │   ├── CourseManagementTest.php
│   │   └── EnrollmentTest.php
│   ├── Instructor/
│   │   ├── LessonManagementTest.php
│   │   ├── QuizManagementTest.php
│   │   └── QuestionBankTest.php
│   └── Student/
│       ├── QuizTakingTest.php
│       └── LessonViewingTest.php
└── Unit/
    ├── Models/
    ├── Services/
    └── Helpers/
```

### Test Coverage Goals
- Overall: 80%+
- Controllers: 90%+
- Models: 95%+
- Services: 85%+

---

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Configure production database
- [ ] Set up SSL certificate
- [ ] Configure queue worker
- [ ] Set up scheduled tasks (cron)
- [ ] Configure email service
- [ ] Set up backup system
- [ ] Configure Redis for caching
- [ ] Optimize autoloader
- [ ] Cache routes and config
- [ ] Set proper file permissions
- [ ] Configure firewall rules
- [ ] Set up monitoring (optional)
- [ ] Test all features in production

### Optimization Commands

```bash
# Clear all caches
php artisan optimize:clear

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Build assets for production
npm run build
```

### Queue Worker Setup

```bash
# Install supervisor
sudo apt-get install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/quiz-lms-worker.conf
```

Add configuration:
```ini
[program:quiz-lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/quiz-lms/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasflags=QUIT
redirect_stderr=true
stdout_logfile=/path/to/quiz-lms/storage/logs/worker.log
```

```bash
# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start quiz-lms-worker:*
```

### Cron Jobs Setup

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /path/to/quiz-lms && php artisan schedule:run >> /dev/null 2>&1
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/quiz-lms/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal test
sudo certbot renew --dry-run
```

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### How to Contribute

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. **Push** to the branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

### Code Style

- Follow PSR-12 coding standard
- Use Laravel best practices
- Write meaningful commit messages
- Add tests for new features
- Update documentation

### Testing Requirements

- All tests must pass
- Add tests for new features
- Maintain 80%+ code coverage

### Pull Request Process

1. Update README.md with details of changes
2. Update documentation if needed
3. The PR will be merged once approved by maintainers

---

## 🔒 Security

### Reporting Vulnerabilities

If you discover a security vulnerability, please email security@quizlms.com. Do not create public GitHub issues for security vulnerabilities.

### Security Features

- ✅ CSRF Protection
- ✅ XSS Protection
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ Password Hashing (bcrypt)
- ✅ Rate Limiting
- ✅ Role-based Access Control
- ✅ Audit Logging
- ✅ Session Management
- ✅ File Upload Validation
- ✅ Input Sanitization

### Security Best Practices

1. **Keep Updated**: Regularly update dependencies
2. **Strong Passwords**: Enforce password complexity
3. **2FA**: Enable two-factor authentication (optional)
4. **HTTPS**: Always use SSL in production
5. **Backups**: Regular automated backups
6. **Monitoring**: Set up security monitoring
7. **Firewall**: Configure firewall rules
8. **Permissions**: Proper file permissions (644 for files, 755 for directories)

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2024 Quiz LMS

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 💬 Support

### Documentation
- [Installation Guide](docs/installation.md)
- [User Manual](docs/user-manual.md)
- [API Reference](docs/api-reference.md)
- [FAQ](docs/faq.md)

### Community
- **GitHub Issues**: [Report bugs or request features](https://github.com/rozuragon/quiz-lms/issues)
- **Discussions**: [Ask questions and share ideas](https://github.com/rozuragon/quiz-lms/discussions)
- **Email**: quizlms.edu@gmail.com

### Professional Support
For professional support, training, or custom development:
- Email: quizlms.edu@gmail.com
- Website: https://quizlms.com

---

## 🙏 Acknowledgments

- **Laravel** - The PHP Framework for Web Artisans
- **Livewire** - A full-stack framework for Laravel
- **Bootstrap** - The world's most popular front-end toolkit
- **SB Admin 2** - Free Bootstrap admin template
- **OpenAI** - AI-powered features
- **Font Awesome** - Icon library

### Built With ❤️ By

- Your Name - Jerrie Jose T. Asistin
- Contributors - [See all contributors](https://github.com/rozuragon/quiz-lms=system/contributors)

---

## 📊 Project Statistics

![GitHub stars](https://img.shields.io/github/stars/yourusername/quiz-lms?style=social)
![GitHub forks](https://img.shields.io/github/forks/yourusername/quiz-lms?style=social)
![GitHub issues](https://img.shields.io/github/issues/yourusername/quiz-lms)
![GitHub license](https://img.shields.io/github/license/yourusername/quiz-lms)

---

## 🗺️ Roadmap

### Version 2.0 (Q2 2025)
- [ ] Mobile app (React Native)
- [ ] Video conferencing integration
- [ ] Advanced analytics with ML
- [ ] Multi-language support
- [ ] Progressive Web App (PWA)
- [ ] Calendar integration
- [ ] Discussion forums

### Version 2.1 (Q3 2025)
- [ ] Gamification features
- [ ] Certificate generation
- [ ] Payment gateway integration
- [ ] Course marketplace
- [ ] API v2 with GraphQL

### Future Ideas
- Virtual classroom
- Attendance tracking
- Parent portal
- Mobile notifications
- Plagiarism detection
- Integration with popular LMS platforms

---

<div align="center">

**⭐ Star this repository if you find it helpful!**

Made with ❤️ using Laravel

[⬆ Back to Top](#-quiz--learning-management-system-lms)

</div>