# 🐘 How to Use PostgreSQL - Complete Guide

A comprehensive guide to using PostgreSQL with your Quiz LMS application on Render.

## 📋 Table of Contents

- [Introduction to PostgreSQL](#-introduction-to-postgresql)
- [Connecting to Your Database](#-connecting-to-your-database)
- [PostgreSQL Basics](#-postgresql-basics)
- [Common SQL Operations](#-common-sql-operations)
- [Laravel & PostgreSQL](#-laravel--postgresql)
- [Database Management on Render](#-database-management-on-render)
- [Quiz LMS Specific Queries](#-quiz-lms-specific-queries)
- [Backup & Restore](#-backup--restore)
- [Performance Optimization](#-performance-optimization)
- [Troubleshooting](#-troubleshooting)
- [Useful Resources](#-useful-resources)

---

## 🎯 Introduction to PostgreSQL

### What is PostgreSQL?

PostgreSQL (often called "Postgres") is a powerful, open-source relational database management system (RDBMS). It's known for:

- ✅ **ACID Compliance**: Ensures data reliability
- ✅ **Advanced Features**: JSON support, full-text search, arrays
- ✅ **Extensibility**: Custom functions, data types, operators
- ✅ **Performance**: Excellent for complex queries
- ✅ **Standards Compliance**: SQL standard compliant

### Why PostgreSQL for Quiz LMS?

Your application uses PostgreSQL because:
- ✅ Better performance for complex queries
- ✅ Advanced data types (JSON, arrays)
- ✅ Excellent for educational data structures
- ✅ Free tier available on Render
- ✅ Production-ready and reliable

### Your Database Configuration

Based on your `render.yaml`:

- **Database Name**: `quizlms`
- **User**: `quizlms_user` (auto-generated)
- **Host**: Auto-configured by Render
- **Port**: 5432 (default PostgreSQL port)
- **Connection**: Auto-linked to web and worker services

---

## 🔌 Connecting to Your Database

### Method 1: Via Render Shell (Recommended)

1. Go to your **web service** dashboard on Render
2. Click **"Shell"** tab
3. Connect using `psql`:

```bash
# Use the DATABASE_URL environment variable
psql $DATABASE_URL

# Or connect manually (credentials are in environment)
psql -h $DB_HOST -p $DB_PORT -U $DB_USERNAME -d $DB_DATABASE
```

### Method 2: Via Laravel Tinker

1. Access Render Shell
2. Run:

```bash
php artisan tinker
```

Then use Laravel's database methods:

```php
// Test connection
DB::connection()->getPdo();

// Run raw SQL
DB::select('SELECT version()');

// Use Eloquent models
User::count();
```

### Method 3: External Database Client

#### Get Connection String from Render

1. Go to your **database service** dashboard
2. Find **"Connection String"** or **"Internal Connection URL"**
3. Copy the connection string

#### Using pgAdmin

1. Download [pgAdmin](https://www.pgadmin.org/)
2. Add new server:
   - **Host**: From Render dashboard
   - **Port**: 5432
   - **Database**: `quizlms`
   - **Username**: From Render dashboard
   - **Password**: From Render dashboard

#### Using DBeaver

1. Download [DBeaver](https://dbeaver.io/)
2. Create new connection → PostgreSQL
3. Enter credentials from Render dashboard
4. Test connection

#### Using VS Code Extensions

1. Install **"PostgreSQL"** extension
2. Add connection using Render credentials
3. Browse and query database

#### Using Command Line (Local)

If you have `psql` installed locally:

```bash
psql "postgresql://username:password@host:port/quizlms"
```

Replace with actual credentials from Render.

---

## 📚 PostgreSQL Basics

### Basic Commands

#### Connect to Database

```sql
-- Connect to specific database
\c quizlms

-- List all databases
\l

-- List all tables
\dt

-- Describe a table structure
\d users

-- List all schemas
\dn

-- Exit psql
\q
```

#### Getting Help

```sql
-- General help
\?

-- SQL command help
\h SELECT

-- psql command help
\?
```

### Data Types

PostgreSQL supports many data types:

| Type | Description | Example |
|------|-------------|---------|
| `VARCHAR(n)` | Variable-length string | `VARCHAR(255)` |
| `TEXT` | Unlimited text | `TEXT` |
| `INTEGER` | 32-bit integer | `INTEGER` |
| `BIGINT` | 64-bit integer | `BIGINT` |
| `BOOLEAN` | True/false | `BOOLEAN` |
| `TIMESTAMP` | Date and time | `TIMESTAMP` |
| `JSON` | JSON data | `JSON` |
| `JSONB` | Binary JSON | `JSONB` |
| `ARRAY` | Array of values | `INTEGER[]` |

### Common Operators

```sql
-- Comparison
=, !=, <>, <, >, <=, >=

-- Logical
AND, OR, NOT

-- Pattern matching
LIKE, ILIKE (case-insensitive), SIMILAR TO

-- NULL handling
IS NULL, IS NOT NULL

-- Array operators
@>, <@, &&, ||
```

---

## 🔧 Common SQL Operations

### SELECT Queries

#### Basic SELECT

```sql
-- Select all columns
SELECT * FROM users;

-- Select specific columns
SELECT id, username, email FROM users;

-- Select with condition
SELECT * FROM users WHERE role = 'admin';

-- Select with multiple conditions
SELECT * FROM users 
WHERE role = 'student' AND status = 'active';

-- Select with ordering
SELECT * FROM users ORDER BY created_at DESC;

-- Select with limit
SELECT * FROM users LIMIT 10;

-- Select with offset (pagination)
SELECT * FROM users LIMIT 10 OFFSET 20;
```

#### Advanced SELECT

```sql
-- Count records
SELECT COUNT(*) FROM users;

-- Count with condition
SELECT COUNT(*) FROM users WHERE role = 'student';

-- Group by
SELECT role, COUNT(*) 
FROM users 
GROUP BY role;

-- Having (filter groups)
SELECT role, COUNT(*) 
FROM users 
GROUP BY role 
HAVING COUNT(*) > 10;

-- Join tables
SELECT u.username, c.name as course_name
FROM users u
INNER JOIN enrollments e ON u.id = e.user_id
INNER JOIN courses c ON e.course_id = c.id;

-- Subquery
SELECT * FROM users 
WHERE id IN (
    SELECT user_id FROM enrollments
);
```

### INSERT Operations

```sql
-- Insert single record
INSERT INTO users (username, email, password, role)
VALUES ('john_doe', 'john@example.com', 'hashed_password', 'student');

-- Insert multiple records
INSERT INTO users (username, email, role) VALUES
('user1', 'user1@example.com', 'student'),
('user2', 'user2@example.com', 'instructor'),
('user3', 'user3@example.com', 'admin');

-- Insert with returning (get inserted record)
INSERT INTO users (username, email, role)
VALUES ('new_user', 'new@example.com', 'student')
RETURNING *;
```

### UPDATE Operations

```sql
-- Update single record
UPDATE users 
SET email = 'newemail@example.com'
WHERE id = 1;

-- Update multiple records
UPDATE users 
SET status = 'active'
WHERE role = 'student';

-- Update with condition
UPDATE users 
SET last_login = NOW()
WHERE id = 1;
```

### DELETE Operations

```sql
-- Delete single record
DELETE FROM users WHERE id = 1;

-- Delete multiple records
DELETE FROM users WHERE status = 'inactive';

-- Delete all records (dangerous!)
DELETE FROM users;

-- Truncate (faster, resets auto-increment)
TRUNCATE TABLE users;
```

### CREATE Operations

```sql
-- Create table
CREATE TABLE example_table (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT NOW()
);

-- Create index
CREATE INDEX idx_users_email ON users(email);

-- Create unique index
CREATE UNIQUE INDEX idx_users_username ON users(username);
```

### ALTER Operations

```sql
-- Add column
ALTER TABLE users ADD COLUMN phone VARCHAR(20);

-- Drop column
ALTER TABLE users DROP COLUMN phone;

-- Modify column
ALTER TABLE users ALTER COLUMN email TYPE VARCHAR(500);

-- Add constraint
ALTER TABLE users ADD CONSTRAINT check_email 
CHECK (email LIKE '%@%');
```

---

## 🎨 Laravel & PostgreSQL

### Laravel Database Configuration

Your application uses PostgreSQL via Laravel's Eloquent ORM. Configuration is in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=[auto-set by Render]
DB_PORT=5432
DB_DATABASE=quizlms
DB_USERNAME=[auto-set by Render]
DB_PASSWORD=[auto-set by Render]
```

### Using Eloquent Models

#### Basic Queries

```php
// Get all users
$users = User::all();

// Get user by ID
$user = User::find(1);

// Get with condition
$students = User::where('role', 'student')->get();

// Get first
$admin = User::where('role', 'admin')->first();

// Count
$count = User::count();

// Create
$user = User::create([
    'username' => 'newuser',
    'email' => 'new@example.com',
    'password' => Hash::make('password'),
    'role' => 'student'
]);

// Update
$user = User::find(1);
$user->email = 'updated@example.com';
$user->save();

// Delete
User::find(1)->delete();
```

#### Advanced Queries

```php
// Eager loading (prevent N+1)
$users = User::with('enrollments.course')->get();

// Joins
$users = User::join('enrollments', 'users.id', '=', 'enrollments.user_id')
    ->select('users.*', 'enrollments.course_id')
    ->get();

// Raw queries
$results = DB::select('SELECT * FROM users WHERE role = ?', ['student']);

// Query builder
$users = DB::table('users')
    ->where('role', 'student')
    ->where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->get();
```

### Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Run migrations (production)
php artisan migrate --force

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh (rollback + migrate)
php artisan migrate:refresh

# Fresh (drop all + migrate)
php artisan migrate:fresh

# Show migration status
php artisan migrate:status
```

### Database Seeders

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder

# Refresh and seed
php artisan migrate:fresh --seed
```

### Using Tinker

```bash
php artisan tinker
```

Then:

```php
// Test connection
DB::connection()->getPdo();

// Query users
User::count();
User::where('role', 'admin')->get();

// Create user
User::create(['username' => 'test', 'email' => 'test@test.com', 'password' => bcrypt('password'), 'role' => 'student']);

// Update
$user = User::first();
$user->update(['email' => 'new@example.com']);

// Delete
User::where('username', 'test')->delete();

// Raw SQL
DB::select('SELECT COUNT(*) as count FROM users');
```

---

## 🗄️ Database Management on Render

### Viewing Database Information

1. Go to Render Dashboard
2. Click on **"quiz-lms-db"** database service
3. View:
   - **Status**: Available/Unavailable
   - **Plan**: Free/Paid tier
   - **Storage**: Used/Total (e.g., 150MB / 1GB)
   - **Region**: Data center location
   - **Connection String**: Internal URL

### Database Credentials

Credentials are automatically provided and linked:

- ✅ **Host**: Auto-configured
- ✅ **Port**: 5432
- ✅ **Database**: `quizlms`
- ✅ **Username**: Auto-generated
- ✅ **Password**: Auto-generated

> **Note**: Don't manually set `DB_*` variables in Render. They're auto-linked!

### Storage Management

#### Check Storage Usage

```sql
-- Check database size
SELECT pg_size_pretty(pg_database_size('quizlms'));

-- Check table sizes
SELECT 
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;
```

#### Free Tier Limits

- **Storage**: 1GB total
- **Retention**: 90 days
- **Backups**: Not available on free tier

#### Managing Storage

```sql
-- Find large tables
SELECT 
    tablename,
    pg_size_pretty(pg_total_relation_size('public.' || tablename)) AS size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size('public.' || tablename) DESC;

-- Clean up old data (example: old quiz attempts)
DELETE FROM quiz_attempts 
WHERE created_at < NOW() - INTERVAL '1 year';

-- Vacuum (reclaim space)
VACUUM FULL;
```

### Database Backups

#### Free Tier

- ❌ No automatic backups
- ❌ No manual backup option
- ⚠️ Data retention: 90 days

#### Paid Tier

- ✅ Automatic daily backups
- ✅ Point-in-time recovery
- ✅ Manual backup option
- ✅ Longer retention

#### Manual Backup (if on paid tier)

1. Go to database dashboard
2. Click **"Backups"** tab
3. Click **"Create Backup"**
4. Download backup file

---

## 📊 Quiz LMS Specific Queries

### User Management

```sql
-- Count users by role
SELECT role, COUNT(*) as count
FROM users
GROUP BY role;

-- Find inactive users
SELECT * FROM users
WHERE status = 'inactive'
ORDER BY updated_at DESC;

-- Find users who haven't logged in recently
SELECT * FROM users
WHERE last_login < NOW() - INTERVAL '30 days'
ORDER BY last_login DESC;
```

### Course & Enrollment

```sql
-- Count enrollments per course
SELECT c.name, COUNT(e.id) as enrollment_count
FROM courses c
LEFT JOIN enrollments e ON c.id = e.course_id
GROUP BY c.id, c.name
ORDER BY enrollment_count DESC;

-- Find students in a specific course
SELECT u.username, u.email, e.enrolled_at
FROM users u
INNER JOIN enrollments e ON u.id = e.user_id
WHERE e.course_id = 1
ORDER BY e.enrolled_at DESC;
```

### Quiz & Results

```sql
-- Quiz completion rate
SELECT 
    q.title,
    COUNT(DISTINCT qa.user_id) as attempts,
    COUNT(DISTINCT CASE WHEN qa.status = 'completed' THEN qa.id END) as completed,
    ROUND(100.0 * COUNT(DISTINCT CASE WHEN qa.status = 'completed' THEN qa.id END) / 
          NULLIF(COUNT(DISTINCT qa.user_id), 0), 2) as completion_rate
FROM quizzes q
LEFT JOIN quiz_attempts qa ON q.id = qa.quiz_id
GROUP BY q.id, q.title
ORDER BY completion_rate DESC;

-- Average scores per quiz
SELECT 
    q.title,
    COUNT(qa.id) as total_attempts,
    ROUND(AVG(qa.score), 2) as avg_score,
    ROUND(MAX(qa.score), 2) as max_score,
    ROUND(MIN(qa.score), 2) as min_score
FROM quizzes q
INNER JOIN quiz_attempts qa ON q.id = qa.quiz_id
WHERE qa.status = 'completed'
GROUP BY q.id, q.title
ORDER BY avg_score DESC;

-- Student performance
SELECT 
    u.username,
    COUNT(qa.id) as total_quizzes,
    ROUND(AVG(qa.score), 2) as avg_score,
    COUNT(CASE WHEN qa.score >= q.passing_score THEN 1 END) as passed
FROM users u
INNER JOIN quiz_attempts qa ON u.id = qa.user_id
INNER JOIN quizzes q ON qa.quiz_id = q.id
WHERE u.role = 'student' AND qa.status = 'completed'
GROUP BY u.id, u.username
ORDER BY avg_score DESC;
```

### Lesson Views

```sql
-- Most viewed lessons
SELECT 
    l.title,
    COUNT(lv.id) as view_count,
    COUNT(DISTINCT lv.user_id) as unique_viewers
FROM lessons l
LEFT JOIN lesson_views lv ON l.id = lv.lesson_id
GROUP BY l.id, l.title
ORDER BY view_count DESC
LIMIT 10;
```

### Question Bank

```sql
-- Questions by type
SELECT 
    type,
    COUNT(*) as count,
    ROUND(100.0 * COUNT(*) / SUM(COUNT(*)) OVER (), 2) as percentage
FROM question_banks
GROUP BY type
ORDER BY count DESC;

-- Questions by difficulty
SELECT 
    difficulty,
    COUNT(*) as count
FROM question_banks
GROUP BY difficulty
ORDER BY 
    CASE difficulty
        WHEN 'easy' THEN 1
        WHEN 'medium' THEN 2
        WHEN 'hard' THEN 3
    END;
```

---

## 💾 Backup & Restore

### Creating Backups

#### Using pg_dump (if available)

```bash
# Backup entire database
pg_dump -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE > backup.sql

# Backup specific table
pg_dump -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE -t users > users_backup.sql

# Backup with compression
pg_dump -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE | gzip > backup.sql.gz
```

#### Using Laravel Backup Package

If you have `spatie/laravel-backup` installed:

```bash
php artisan backup:run
```

### Restoring Backups

```bash
# Restore from SQL file
psql -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE < backup.sql

# Restore from compressed backup
gunzip < backup.sql.gz | psql -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE
```

### Exporting Data

```sql
-- Export to CSV (using COPY)
COPY (SELECT * FROM users) TO '/tmp/users.csv' WITH CSV HEADER;

-- Export specific columns
COPY (
    SELECT id, username, email, role 
    FROM users
) TO '/tmp/users.csv' WITH CSV HEADER;
```

---

## ⚡ Performance Optimization

### Indexes

#### Check Existing Indexes

```sql
-- List all indexes
SELECT 
    tablename,
    indexname,
    indexdef
FROM pg_indexes
WHERE schemaname = 'public'
ORDER BY tablename, indexname;
```

#### Create Indexes

```sql
-- Single column index
CREATE INDEX idx_users_email ON users(email);

-- Composite index
CREATE INDEX idx_enrollments_user_course ON enrollments(user_id, course_id);

-- Partial index (only for specific conditions)
CREATE INDEX idx_active_users ON users(email) WHERE status = 'active';

-- Unique index
CREATE UNIQUE INDEX idx_users_username ON users(username);
```

#### Check Index Usage

```sql
-- Find unused indexes
SELECT 
    schemaname,
    tablename,
    indexname,
    idx_scan as index_scans
FROM pg_stat_user_indexes
WHERE idx_scan = 0
ORDER BY pg_relation_size(indexrelid) DESC;
```

### Query Optimization

#### EXPLAIN ANALYZE

```sql
-- Analyze query performance
EXPLAIN ANALYZE
SELECT * FROM users WHERE email = 'test@example.com';

-- Check if index is used
EXPLAIN (ANALYZE, BUFFERS)
SELECT * FROM quiz_attempts WHERE quiz_id = 1;
```

#### Common Optimizations

1. **Add indexes** on frequently queried columns
2. **Use LIMIT** when you don't need all records
3. **Avoid SELECT *** - select only needed columns
4. **Use JOINs** instead of multiple queries
5. **Use EXISTS** instead of IN for large subqueries

### Vacuum and Analyze

```sql
-- Vacuum (clean up dead tuples)
VACUUM;

-- Vacuum with analyze (update statistics)
VACUUM ANALYZE;

-- Vacuum specific table
VACUUM ANALYZE users;

-- Full vacuum (reclaim space, locks table)
VACUUM FULL;
```

---

## 🐛 Troubleshooting

### Connection Issues

#### Problem: Cannot connect to database

**Solution:**
1. Check database service is running on Render
2. Verify environment variables are set:
   ```bash
   echo $DB_HOST
   echo $DB_PORT
   echo $DB_DATABASE
   ```
3. Test connection:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

#### Problem: Authentication failed

**Solution:**
1. Credentials are auto-set by Render - don't change them
2. Check database service is available
3. Wait 2-3 minutes after database creation

### Query Performance Issues

#### Problem: Slow queries

**Solution:**
1. Use `EXPLAIN ANALYZE` to identify bottlenecks
2. Add indexes on frequently queried columns
3. Optimize queries (avoid N+1, use eager loading)
4. Check table sizes and consider archiving old data

#### Problem: High memory usage

**Solution:**
1. Check for large tables:
   ```sql
   SELECT pg_size_pretty(pg_total_relation_size('table_name'));
   ```
2. Archive old data
3. Add pagination to queries
4. Use `LIMIT` in queries

### Data Issues

#### Problem: Data not appearing

**Solution:**
1. Check if transaction was committed
2. Verify you're querying the correct database
3. Check for soft deletes (if using)
4. Verify user permissions

#### Problem: Foreign key constraint errors

**Solution:**
1. Check referenced records exist
2. Delete in correct order (child records first)
3. Use CASCADE deletes if appropriate

### Migration Issues

#### Problem: Migration fails

**Solution:**
1. Check migration syntax
2. Verify database connection
3. Check for conflicting migrations
4. Review migration logs

#### Problem: Column already exists

**Solution:**
1. Check if migration already ran:
   ```bash
   php artisan migrate:status
   ```
2. Rollback if needed:
   ```bash
   php artisan migrate:rollback
   ```

---

## 📚 Useful Resources

### PostgreSQL Documentation
- [Official Docs](https://www.postgresql.org/docs/)
- [SQL Tutorial](https://www.postgresql.org/docs/current/tutorial.html)
- [Performance Tips](https://www.postgresql.org/docs/current/performance-tips.html)

### Laravel Database
- [Laravel Database Docs](https://laravel.com/docs/database)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Query Builder](https://laravel.com/docs/queries)

### Tools
- [pgAdmin](https://www.pgadmin.org/) - GUI for PostgreSQL
- [DBeaver](https://dbeaver.io/) - Universal database tool
- [TablePlus](https://tableplus.com/) - Modern database client
- [Postico](https://eggerapps.at/postico/) - macOS PostgreSQL client

### Learning Resources
- [PostgreSQL Tutorial](https://www.postgresqltutorial.com/)
- [SQLBolt](https://sqlbolt.com/) - Interactive SQL tutorial
- [PostgreSQL Exercises](https://pgexercises.com/)

---

## ✅ Quick Reference

### Essential Commands

```sql
-- Connect
\c quizlms

-- List tables
\dt

-- Describe table
\d users

-- Show all databases
\l

-- Exit
\q
```

### Common Queries

```sql
-- Count records
SELECT COUNT(*) FROM users;

-- Find by ID
SELECT * FROM users WHERE id = 1;

-- Update
UPDATE users SET email = 'new@example.com' WHERE id = 1;

-- Delete
DELETE FROM users WHERE id = 1;
```

### Laravel Commands

```bash
# Migrate
php artisan migrate

# Seed
php artisan db:seed

# Tinker
php artisan tinker

# Check connection
php artisan tinker
DB::connection()->getPdo();
```

---

**Last Updated**: 2024  
**PostgreSQL Version**: Latest (via Render)  
**Application**: Quiz LMS System

---

*This guide covers PostgreSQL essentials for your Quiz LMS. For advanced topics, refer to PostgreSQL official documentation.*

