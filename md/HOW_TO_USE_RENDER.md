# 📖 How to Use Render - Complete Guide

A comprehensive guide to using Render.com for managing and operating your Quiz LMS application.

## 📋 Table of Contents

- [Getting Started with Render Dashboard](#-getting-started-with-render-dashboard)
- [Understanding Your Services](#-understanding-your-services)
- [Managing Services](#-managing-services)
- [Environment Variables](#-environment-variables)
- [Viewing Logs](#-viewing-logs)
- [Using the Shell](#-using-the-shell)
- [Deployments](#-deployments)
- [Monitoring & Metrics](#-monitoring--metrics)
- [Database Management](#-database-management)
- [Common Operations](#-common-operations)
- [Tips & Best Practices](#-tips--best-practices)
- [Keyboard Shortcuts](#-keyboard-shortcuts)

---

## 🎯 Getting Started with Render Dashboard

### Accessing Your Dashboard

1. Go to [dashboard.render.com](https://dashboard.render.com)
2. Sign in with your GitHub account or email
3. You'll see your **Dashboard** with all services

### Dashboard Overview

The dashboard shows:

- **Services**: All your web services, workers, and databases
- **Status Indicators**: 
  - 🟢 **Live** - Service is running
  - 🟡 **Updating** - Deployment in progress
  - 🔴 **Stopped** - Service is stopped
  - ⚪ **Suspended** - Service is suspended (free tier inactivity)

### Navigation

- **Dashboard**: Overview of all services
- **Services**: List of all services
- **Databases**: List of all databases
- **Settings**: Account settings and billing

---

## 🏗️ Understanding Your Services

### Service Types in Your Application

Your Quiz LMS uses three service types:

#### 1. Web Service (`quiz-lms-web`)
- **Purpose**: Serves your Laravel application
- **Type**: Web Service
- **Runtime**: Docker
- **Port**: 8000 (automatically mapped)
- **Status**: Should be "Live" when running

#### 2. Worker Service (`quiz-lms-worker`)
- **Purpose**: Processes background jobs (emails, notifications)
- **Type**: Background Worker
- **Runtime**: Docker
- **Command**: `php artisan queue:work`
- **Status**: Should be "Live" when running

#### 3. PostgreSQL Database (`quiz-lms-db`)
- **Purpose**: Stores all application data
- **Type**: PostgreSQL
- **Plan**: Free tier (1GB) or paid
- **Status**: Should be "Available"

### Service Status Meanings

| Status | Meaning | Action Needed |
|--------|---------|---------------|
| 🟢 **Live** | Running normally | None |
| 🟡 **Updating** | Deployment in progress | Wait for completion |
| 🔴 **Stopped** | Manually stopped | Click "Manual Deploy" to start |
| ⚪ **Suspended** | Spun down (free tier) | First request will wake it up |
| 🔴 **Build Failed** | Deployment error | Check logs and fix issues |

---

## ⚙️ Managing Services

### Viewing Service Details

1. Click on any service name in the dashboard
2. You'll see:
   - **Overview**: Status, URL, plan, region
   - **Logs**: Real-time application logs
   - **Metrics**: CPU, memory, request metrics
   - **Environment**: Environment variables
   - **Settings**: Configuration options
   - **Shell**: Command-line access
   - **Events**: Deployment history

### Starting a Stopped Service

1. Go to service dashboard
2. Click **"Manual Deploy"** button
3. Wait for deployment to complete (2-5 minutes)

### Stopping a Service

1. Go to service dashboard
2. Click **"Settings"** tab
3. Scroll to **"Danger Zone"**
4. Click **"Suspend Service"**
5. Confirm the action

> **Warning**: Stopping a service makes it unavailable. Only stop for maintenance.

### Restarting a Service

1. Go to service dashboard
2. Click **"Manual Deploy"** button
3. Or use the **"Restart"** option in Settings

### Deleting a Service

1. Go to service dashboard
2. Click **"Settings"** tab
3. Scroll to **"Danger Zone"**
4. Click **"Delete Service"**
5. Type service name to confirm

> **⚠️ WARNING**: This permanently deletes the service and all its data!

---

## 🔐 Environment Variables

### Viewing Environment Variables

1. Go to service dashboard
2. Click **"Environment"** tab
3. See all configured variables

### Adding Environment Variables

1. Go to **"Environment"** tab
2. Click **"Add Environment Variable"**
3. Enter:
   - **Key**: Variable name (e.g., `APP_KEY`)
   - **Value**: Variable value
4. Click **"Save Changes"**
5. Service will automatically redeploy

### Editing Environment Variables

1. Find the variable in the list
2. Click the **pencil icon** (✏️)
3. Update the value
4. Click **"Save Changes"**

### Deleting Environment Variables

1. Find the variable in the list
2. Click the **trash icon** (🗑️)
3. Confirm deletion
4. Service will redeploy

### Environment Variable Sync

In your `render.yaml`, the worker service inherits variables from the web service:

```yaml
- fromService:
    type: web
    name: quiz-lms-web
    property: envVars
```

This means:
- ✅ Variables set in web service automatically sync to worker
- ✅ You only need to set variables once (in web service)
- ✅ Changes to web service variables update worker automatically

### Common Environment Variables

| Variable | Service | Purpose |
|----------|---------|---------|
| `APP_KEY` | Web, Worker | Laravel encryption key |
| `APP_URL` | Web | Application URL |
| `DB_*` | Web, Worker | Database connection (auto-set) |
| `MAIL_*` | Web, Worker | Email configuration |
| `OPENAI_API_KEY` | Web, Worker | OpenAI API key |

---

## 📊 Viewing Logs

### Accessing Logs

1. Go to service dashboard
2. Click **"Logs"** tab
3. See real-time log output

### Log Features

- **Real-time Updates**: Logs stream automatically
- **Search**: Use Ctrl+F (Cmd+F on Mac) to search
- **Filter**: Filter by log level (if supported)
- **Download**: Click download icon to save logs
- **Auto-scroll**: Automatically scrolls to latest logs

### Understanding Logs

#### Web Service Logs

Look for:
- `Starting Laravel server on port 8000` - Server started
- `Database connection successful!` - DB connected
- `Running database migrations...` - Migrations running
- `Seeding database...` - Database seeding
- Error messages - Issues to fix

#### Worker Service Logs

Look for:
- `Processing: App\Jobs\SendWelcomeEmailJob` - Job processing
- `Processed: App\Jobs\SendWelcomeEmailJob` - Job completed
- `Failed: App\Jobs\...` - Job failed (check error)

### Common Log Patterns

**Successful Startup:**
```
Starting Laravel Application Setup
Waiting for database connection...
Database connection successful!
Running database migrations...
Starting Laravel server on port 8000
```

**Error Pattern:**
```
ERROR: [error message]
at [file path]:[line number]
```

### Downloading Logs

1. Click the **download icon** (⬇️) in logs tab
2. Logs are downloaded as a text file
3. Useful for debugging or sharing with support

---

## 💻 Using the Shell

### Accessing the Shell

1. Go to service dashboard
2. Click **"Shell"** tab
3. A terminal opens in your browser

### Shell Capabilities

You can run:
- ✅ Laravel Artisan commands
- ✅ PHP commands
- ✅ File system operations
- ✅ Database commands (via `php artisan tinker`)

### Common Shell Commands

#### Laravel Commands

```bash
# Check Laravel version
php artisan --version

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate application key
php artisan key:generate --show

# Create storage link
php artisan storage:link

# List routes
php artisan route:list

# Run queue worker (if not using background worker)
php artisan queue:work
```

#### Database Operations

```bash
# Access Laravel Tinker (interactive shell)
php artisan tinker

# In Tinker, you can:
DB::table('users')->count();
User::all();
```

#### File Operations

```bash
# List files
ls -la

# Check storage permissions
ls -la storage/

# Check disk space
df -h

# View file contents
cat .env
```

### Shell Limitations

- ❌ Cannot install system packages
- ❌ Cannot modify Dockerfile
- ❌ Limited to application directory
- ❌ No sudo access

### Exiting Shell

- Type `exit` or press Ctrl+D
- Or close the browser tab

---

## 🚀 Deployments

### Understanding Deployments

Every time you:
- Push code to GitHub (if auto-deploy enabled)
- Manually trigger deployment
- Change environment variables
- Update service settings

Render creates a new deployment.

### Viewing Deployment History

1. Go to service dashboard
2. Click **"Events"** tab
3. See all deployments with:
   - Status (✅ Success, ❌ Failed)
   - Commit message
   - Deployment time
   - Duration

### Manual Deployment

1. Go to service dashboard
2. Click **"Manual Deploy"** button
3. Select branch (usually `main`)
4. Click **"Deploy latest commit"**
5. Watch deployment progress in **"Logs"** tab

### Auto-Deploy Configuration

1. Go to service dashboard
2. Click **"Settings"** tab
3. Scroll to **"Auto-Deploy"**
4. Configure:
   - **Auto-Deploy**: Yes/No
   - **Branch**: Which branch to deploy (e.g., `main`)
5. Click **"Save Changes"**

### Deployment Status

| Status | Meaning |
|--------|---------|
| ✅ **Live** | Deployment successful, service running |
| 🟡 **Building** | Docker image building |
| 🟡 **Deploying** | Deploying new version |
| ❌ **Failed** | Deployment failed, check logs |

### Rollback Deployment

1. Go to **"Events"** tab
2. Find a previous successful deployment
3. Click **"Deploy this commit"**
4. Confirm rollback

---

## 📈 Monitoring & Metrics

### Accessing Metrics

1. Go to service dashboard
2. Click **"Metrics"** tab
3. View real-time metrics

### Available Metrics

#### CPU Usage
- Shows CPU utilization over time
- High CPU = service is working hard
- Normal: 10-50%, High: 70%+

#### Memory Usage
- Shows RAM consumption
- Free tier: 512MB limit
- Watch for memory leaks

#### Request Rate
- Requests per second/minute
- Shows traffic patterns
- Useful for performance analysis

#### Response Time
- Average response time
- Lower is better
- Normal: <500ms, Slow: >2s

### Setting Up Alerts

1. Go to service dashboard
2. Click **"Settings"** tab
3. Scroll to **"Alerts"**
4. Configure:
   - CPU threshold
   - Memory threshold
   - Error rate threshold
5. Add email for notifications

### Interpreting Metrics

**Healthy Service:**
- CPU: 10-30% average
- Memory: <400MB (on 512MB plan)
- Response time: <500ms
- Error rate: <1%

**Unhealthy Service:**
- CPU: >80% consistently
- Memory: >450MB (approaching limit)
- Response time: >2s
- Error rate: >5%

---

## 🗄️ Database Management

### Accessing Database

1. Go to **Dashboard**
2. Click on your database service (`quiz-lms-db`)
3. View database details

### Database Information

You'll see:
- **Connection String**: Internal connection URL
- **Status**: Available/Unavailable
- **Plan**: Free/Paid tier
- **Storage**: Used/Total storage
- **Region**: Data center location

### Database Credentials

Credentials are automatically provided:
- **Host**: Auto-configured
- **Port**: Auto-configured
- **Database**: `quizlms` (from render.yaml)
- **Username**: Auto-generated
- **Password**: Auto-generated

> **Note**: Credentials are auto-linked to your services. Don't set them manually!

### Connecting to Database

#### Via Shell (Recommended)

```bash
# Access web service shell
# Then use Laravel Tinker:
php artisan tinker

# Or use psql directly:
psql $DATABASE_URL
```

#### Via External Tool

1. Get connection string from database dashboard
2. Use tools like:
   - pgAdmin
   - DBeaver
   - TablePlus
   - VS Code extensions

### Database Backups

#### Free Tier
- ❌ No automatic backups
- ❌ Manual backups not available
- ⚠️ Data retention: 90 days

#### Paid Tier
- ✅ Automatic daily backups
- ✅ Point-in-time recovery
- ✅ Manual backup option
- ✅ Longer retention

### Database Maintenance

Render handles:
- ✅ Automatic updates
- ✅ Security patches
- ✅ Performance optimization
- ✅ Connection pooling

You handle:
- ✅ Application-level backups (if needed)
- ✅ Data migrations
- ✅ Index optimization (via migrations)

---

## 🔧 Common Operations

### Clearing Application Cache

**Via Shell:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Via Environment Variable:**
Add temporary variable to trigger cache clear, then remove it.

### Running Database Migrations

**Via Shell:**
```bash
php artisan migrate --force
```

**Automatic:**
Migrations run automatically on deployment via `start.sh`.

### Seeding Database

**Via Shell:**
```bash
php artisan db:seed --force
```

**Specific Seeder:**
```bash
php artisan db:seed --class=UserSeeder --force
```

### Checking Application Status

**Via Shell:**
```bash
# Check Laravel is working
php artisan --version

# Check database connection
php artisan tinker
# Then: DB::connection()->getPdo();
```

### Viewing Application URL

1. Go to web service dashboard
2. **Overview** tab shows:
   - **URL**: Your application URL
   - Click to open in new tab

### Testing Email Sending

**Via Shell:**
```bash
php artisan tinker
# Then:
Mail::raw('Test email', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

### Checking Queue Status

**Via Shell:**
```bash
php artisan queue:work --once
```

Or check worker service logs for job processing.

### Viewing Failed Jobs

**Via Shell:**
```bash
php artisan queue:failed
```

### Retrying Failed Jobs

**Via Shell:**
```bash
# Retry all failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry [job-id]
```

---

## 💡 Tips & Best Practices

### Performance Optimization

1. **Enable Caching**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Monitor Metrics**
   - Check CPU and memory regularly
   - Optimize if consistently high

3. **Database Optimization**
   - Add indexes for frequently queried columns
   - Use eager loading to reduce queries

### Security Best Practices

1. **Environment Variables**
   - Never commit secrets to Git
   - Use Render environment variables
   - Rotate keys regularly

2. **Application Key**
   - Generate unique `APP_KEY` for production
   - Never share or expose

3. **Database Access**
   - Use internal connection (auto-configured)
   - Don't expose database publicly

### Cost Optimization

1. **Free Tier**
   - Services spin down after inactivity
   - Use for development/testing
   - Not recommended for production

2. **Paid Tier**
   - Services stay awake 24/7
   - Better performance
   - Automatic backups

3. **Resource Usage**
   - Monitor metrics to right-size plans
   - Start small, scale up as needed

### Development Workflow

1. **Local Development**
   - Develop and test locally
   - Use Git for version control

2. **Staging**
   - Create separate Render services for staging
   - Test before production

3. **Production**
   - Enable auto-deploy only for main branch
   - Monitor deployments closely
   - Keep backups

### Troubleshooting Workflow

1. **Check Logs First**
   - Web service logs for application errors
   - Worker logs for queue issues
   - Database logs for connection problems

2. **Verify Environment Variables**
   - All required variables set
   - Values are correct
   - No typos

3. **Test in Shell**
   - Run commands manually
   - Check database connection
   - Verify file permissions

4. **Check Service Status**
   - Services are running
   - No deployment failures
   - Resources not exhausted

---

## ⌨️ Keyboard Shortcuts

### Dashboard Navigation

- **G + D**: Go to Dashboard
- **G + S**: Go to Services
- **G + B**: Go to Databases
- **G + T**: Go to Settings

### Logs View

- **Ctrl + F** (Cmd + F): Search logs
- **Ctrl + L**: Clear logs (in some views)
- **Arrow Keys**: Navigate log history

### Shell

- **Ctrl + C**: Cancel current command
- **Ctrl + D**: Exit shell
- **Ctrl + L**: Clear screen
- **Tab**: Auto-complete
- **Up/Down**: Command history

---

## 🆘 Getting Help

### Render Support

1. **Documentation**: [render.com/docs](https://render.com/docs)
2. **Community Forum**: [community.render.com](https://community.render.com)
3. **Status Page**: [status.render.com](https://status.render.com)
4. **Support Email**: Available for paid plans

### Application-Specific Help

1. **Check Logs**: First step for any issue
2. **Review Setup Guide**: `RENDER_SETUP.md`
3. **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)
4. **GitHub Issues**: Report bugs in your repository

### Common Support Scenarios

**Service Won't Start:**
- Check logs for errors
- Verify environment variables
- Check Docker build logs

**Database Connection Failed:**
- Verify database is running
- Check database credentials (auto-set)
- Wait 2-3 minutes after database creation

**Deployment Failed:**
- Check build logs
- Verify all files are committed
- Check Dockerfile is correct

---

## ✅ Quick Reference Checklist

### Daily Operations

- [ ] Check service status (all services Live)
- [ ] Monitor metrics (CPU, memory normal)
- [ ] Review logs (no errors)
- [ ] Verify application is accessible

### Weekly Operations

- [ ] Review deployment history
- [ ] Check database storage usage
- [ ] Review error logs
- [ ] Update dependencies (if needed)

### Monthly Operations

- [ ] Review costs and usage
- [ ] Optimize performance
- [ ] Update documentation
- [ ] Review security settings

---

## 🎓 Learning Resources

### Render Documentation
- [Getting Started](https://render.com/docs/getting-started)
- [Docker Guide](https://render.com/docs/docker)
- [Environment Variables](https://render.com/docs/environment-variables)
- [PostgreSQL Guide](https://render.com/docs/databases)

### Video Tutorials
- Render YouTube channel
- Laravel deployment tutorials
- Docker basics

### Community
- Render Discord server
- Stack Overflow (tag: render)
- Reddit r/render

---

**Last Updated**: 2024  
**Render Version**: Latest  
**Application**: Quiz LMS System

---

*This guide covers the essentials of using Render. For advanced topics, refer to Render's official documentation.*

