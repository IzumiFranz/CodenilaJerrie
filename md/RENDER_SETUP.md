# 🚀 Render Hosting Setup Guide

Complete step-by-step guide to deploy your Quiz LMS application on Render.com.

## 📋 Table of Contents

- [Prerequisites](#-prerequisites)
- [Quick Start](#-quick-start)
- [Detailed Setup Steps](#-detailed-setup-steps)
- [Environment Variables](#-environment-variables)
- [Post-Deployment Configuration](#-post-deployment-configuration)
- [Troubleshooting](#-troubleshooting)
- [Free Tier Limitations](#-free-tier-limitations)
- [Upgrading to Paid Plans](#-upgrading-to-paid-plans)

---

## ✅ Prerequisites

Before you begin, ensure you have:

- ✅ A GitHub account
- ✅ Your code pushed to a GitHub repository
- ✅ A Render.com account (sign up at [render.com](https://render.com))
- ✅ Gmail account for email sending (or another SMTP provider)
- ✅ OpenAI API key (optional, for AI features)

---

## ⚡ Quick Start

If you're familiar with Render, here's the quick version:

1. **Push code to GitHub**
2. **Go to Render Dashboard** → New → Blueprint
3. **Connect your repository**
4. **Render auto-detects `render.yaml`** and creates services
5. **Set environment variables** in the web service
6. **Deploy!**

For detailed instructions, continue reading below.

---

## 📝 Detailed Setup Steps

### Step 1: Prepare Your Repository

#### 1.1 Ensure All Files Are Committed

Make sure your repository includes these essential files:

- ✅ `render.yaml` - Render service configuration
- ✅ `Dockerfile` - Docker build configuration
- ✅ `start.sh` - Application startup script
- ✅ `composer.json` - PHP dependencies
- ✅ `package.json` - Node.js dependencies
- ✅ All application source files

#### 1.2 Push to GitHub

```bash
# If you haven't already, initialize git
git init

# Add all files
git add .

# Commit changes
git commit -m "Prepare for Render deployment"

# Add your GitHub remote (replace with your repo URL)
git remote add origin https://github.com/yourusername/quiz-lms-system.git

# Push to GitHub
git push -u origin main
```

> **Note**: Replace `yourusername` and `quiz-lms-system` with your actual GitHub username and repository name.

---

### Step 2: Create Render Account

1. Go to [render.com](https://render.com)
2. Click **"Get Started for Free"**
3. Sign up with GitHub (recommended) or email
4. Verify your email if required

---

### Step 3: Deploy Using Blueprint

#### 3.1 Create New Blueprint

1. In Render Dashboard, click **"New +"** button
2. Select **"Blueprint"**
3. Connect your GitHub account if not already connected
4. Select your repository: `quiz-lms-system`
5. Render will automatically detect `render.yaml`

#### 3.2 Review Services

Render will create 3 services based on `render.yaml`:

1. **Web Service** (`quiz-lms-web`)
   - Serves your Laravel application
   - Handles HTTP requests
   - Runs on port 8000

2. **Worker Service** (`quiz-lms-worker`)
   - Processes background jobs (emails, notifications)
   - Runs queue workers
   - Required for email sending

3. **PostgreSQL Database** (`quiz-lms-db`)
   - Free tier: 1GB storage, 90-day retention
   - Automatically configured

#### 3.3 Apply Blueprint

1. Review the services configuration
2. Click **"Apply"** to start deployment
3. Wait for services to be created (2-5 minutes)

---

### Step 4: Configure Environment Variables

After services are created, you need to set environment variables manually.

#### 4.1 Access Web Service Settings

1. Go to your **Dashboard**
2. Click on **"quiz-lms-web"** service
3. Navigate to **"Environment"** tab

#### 4.2 Set Required Variables

Click **"Add Environment Variable"** for each:

##### Application Configuration

| Variable | Description | Example Value |
|----------|-------------|---------------|
| `APP_KEY` | Laravel encryption key | Generate using: `php artisan key:generate` |
| `APP_URL` | Your Render app URL | `https://quiz-lms-web.onrender.com` |

**To generate APP_KEY:**
```bash
# Option 1: Use Render Shell (recommended)
# Go to your web service → Shell tab, then run:
php artisan key:generate --show
# Copy the output and paste as APP_KEY value

# Option 2: Generate locally
php artisan key:generate --show
# Copy the output
```

##### Email Configuration (Gmail)

| Variable | Description | How to Get |
|----------|-------------|------------|
| `MAIL_USERNAME` | Your Gmail address | `your-email@gmail.com` |
| `MAIL_PASSWORD` | Gmail App Password | See instructions below |
| `MAIL_FROM_ADDRESS` | Sender email | `your-email@gmail.com` |

**Setting up Gmail App Password:**

1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Enable **2-Step Verification** (required)
3. Go to **Security** → **2-Step Verification** → **App passwords**
4. Generate a new app password for "Mail"
5. Copy the 16-character password (no spaces)
6. Use this as `MAIL_PASSWORD`

**Alternative Email Providers:**

If not using Gmail, update these in `render.yaml`:
- `MAIL_HOST` - Your SMTP host
- `MAIL_PORT` - SMTP port (usually 587 or 465)
- `MAIL_ENCRYPTION` - `tls` or `ssl`

##### OpenAI Configuration (Optional)

| Variable | Description | How to Get |
|----------|-------------|------------|
| `OPENAI_API_KEY` | OpenAI API key | Get from [platform.openai.com](https://platform.openai.com/api-keys) |

> **Note**: AI features won't work without this key, but the app will still function.

#### 4.3 Sync Variables to Worker

The worker service automatically inherits environment variables from the web service (configured in `render.yaml`). No manual configuration needed!

---

### Step 5: Initial Database Setup

#### 5.1 Wait for First Deployment

The `start.sh` script automatically:
- ✅ Waits for database connection
- ✅ Runs migrations
- ✅ Seeds database (if empty)
- ✅ Creates storage link
- ✅ Caches configuration

**First deployment takes 5-10 minutes** (Docker build + setup).

#### 5.2 Verify Deployment

1. Check **"Logs"** tab in your web service
2. Look for: `"Starting Laravel server on port 8000"`
3. If you see errors, see [Troubleshooting](#-troubleshooting)

#### 5.3 Access Your Application

1. Go to your web service dashboard
2. Click the URL (e.g., `https://quiz-lms-web.onrender.com`)
3. You should see the login page

---

### Step 6: First Login

#### 6.1 Default Credentials

After database seeding, use these credentials:

```
Username: admin
Password: password
```

> **⚠️ SECURITY WARNING**: Change this password immediately after first login!

#### 6.2 Change Admin Password

1. Login with default credentials
2. Go to **Profile** or **Settings**
3. Change password to a strong password
4. Logout and login again to verify

---

## 🔧 Environment Variables Reference

### Complete List

All environment variables are defined in `render.yaml`. Here's what each does:

#### Application

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `APP_ENV` | No | `production` | Application environment |
| `APP_DEBUG` | No | `false` | Debug mode (keep false in production) |
| `APP_KEY` | **Yes** | - | Laravel encryption key |
| `APP_URL` | **Yes** | - | Your application URL |

#### Database (Auto-configured)

| Variable | Source | Description |
|----------|--------|-------------|
| `DB_CONNECTION` | `render.yaml` | `pgsql` |
| `DB_HOST` | Auto | From database service |
| `DB_PORT` | Auto | From database service |
| `DB_DATABASE` | `render.yaml` | `quizlms` |
| `DB_USERNAME` | Auto | From database service |
| `DB_PASSWORD` | Auto | From database service |

#### Email

| Variable | Required | Description |
|----------|----------|-------------|
| `MAIL_MAILER` | No | `smtp` |
| `MAIL_HOST` | No | `smtp.gmail.com` |
| `MAIL_PORT` | No | `587` |
| `MAIL_USERNAME` | **Yes** | Gmail address |
| `MAIL_PASSWORD` | **Yes** | Gmail app password |
| `MAIL_ENCRYPTION` | No | `tls` |
| `MAIL_FROM_ADDRESS` | **Yes** | Sender email |
| `MAIL_FROM_NAME` | No | `Quiz LMS` |

#### OpenAI (Optional)

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `OPENAI_API_KEY` | No | - | Your OpenAI API key |
| `OPENAI_MODEL` | No | `gpt-4` | Model to use |
| `OPENAI_MAX_TOKENS` | No | `2000` | Max tokens per request |
| `OPENAI_TEMPERATURE` | No | `0.7` | Creativity level (0-1) |

---

## 🎯 Post-Deployment Configuration

### 1. Verify All Services Are Running

Check your Render dashboard:

- ✅ **Web Service**: Status should be "Live"
- ✅ **Worker Service**: Status should be "Live"
- ✅ **Database**: Status should be "Available"

### 2. Test Core Features

- [ ] Login works
- [ ] Dashboard loads
- [ ] Database queries work
- [ ] File uploads work
- [ ] Email sending works (test notification)

### 3. Configure Custom Domain (Optional)

1. Go to your web service → **Settings**
2. Scroll to **"Custom Domains"**
3. Add your domain
4. Follow DNS configuration instructions
5. SSL certificate is automatically provisioned

### 4. Set Up Auto-Deploy

1. Go to web service → **Settings**
2. Under **"Auto-Deploy"**, select:
   - **"Yes"** - Deploy on every push
   - **Branch**: `main` (or your default branch)
3. Repeat for worker service

### 5. Monitor Logs

- **Web Service Logs**: Application errors, requests
- **Worker Service Logs**: Queue job processing
- **Database Logs**: Query performance (if enabled)

---

## 🐛 Troubleshooting

### Problem: Application Shows 500 Error

**Solution:**
1. Check **Logs** tab in web service
2. Common causes:
   - Missing `APP_KEY` → Generate and set it
   - Database connection failed → Check database is running
   - Missing environment variables → Set all required vars
3. Clear caches using Shell:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

### Problem: Database Connection Failed

**Solution:**
1. Verify database service is running
2. Check environment variables are set correctly
3. Database credentials are auto-linked - don't set them manually
4. Wait 2-3 minutes after database creation for it to be ready

### Problem: Assets Not Loading (CSS/JS missing)

**Solution:**
1. Check if `npm run build` completed during Docker build
2. Verify `public/build` directory exists
3. Clear browser cache
4. Check build logs in deployment history

### Problem: Email Not Sending

**Solution:**
1. Verify `MAIL_USERNAME` and `MAIL_PASSWORD` are set
2. Check Gmail app password is correct (16 characters, no spaces)
3. Ensure 2-Step Verification is enabled on Gmail
4. Check worker service is running (emails use queue)
5. Test email in application or check worker logs

### Problem: Queue Jobs Not Processing

**Solution:**
1. Verify worker service is running
2. Check worker service has same environment variables
3. Verify `QUEUE_CONNECTION=database` in environment
4. Check worker service logs for errors

### Problem: Slow First Request After Inactivity

**Solution:**
This is normal on free tier. Services spin down after 15 minutes of inactivity. First request wakes them up (30-60 seconds).

**Workarounds:**
- Use a paid plan (services stay awake)
- Set up a cron job to ping your app every 10 minutes
- Use a service like UptimeRobot (free) to keep services awake

### Problem: Storage/File Upload Issues

**Solution:**
1. Verify storage directory has correct permissions:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```
2. Check storage link exists:
   ```bash
   php artisan storage:link
   ```
3. On free tier, storage is ephemeral (lost on redeploy)
   - Consider using S3 or other cloud storage for production

### Problem: Docker Build Fails

**Solution:**
1. Check build logs for specific error
2. Common issues:
   - Missing `package.json` or `composer.json`
   - Node.js version mismatch
   - PHP extension missing
3. Test Docker build locally:
   ```bash
   docker build -t quiz-lms .
   ```

### Problem: Migrations Fail

**Solution:**
1. Check database is ready (wait 2-3 minutes after creation)
2. Verify database credentials
3. Run migrations manually in Shell:
   ```bash
   php artisan migrate --force
   ```

---

## 💰 Free Tier Limitations

### Render Free Tier Limits

- **Web Services**: 
  - Spins down after 15 minutes of inactivity
  - 750 hours/month total runtime
  - 512MB RAM
  - Shared CPU

- **Worker Services**:
  - Same limits as web services
  - Spins down with web service

- **Databases**:
  - 1GB storage
  - 90-day retention
  - Shared CPU
  - No automatic backups

### Impact on Your Application

1. **Cold Starts**: First request after inactivity takes 30-60 seconds
2. **Data Loss Risk**: Ephemeral storage, limited database retention
3. **Performance**: Shared resources, may be slower during peak times
4. **Reliability**: Services may restart unexpectedly

### Recommendations for Production

- ✅ Use paid plan for production applications
- ✅ Set up external backups for database
- ✅ Use cloud storage (S3) for file uploads
- ✅ Monitor service uptime
- ✅ Set up alerts for service failures

---

## 🚀 Upgrading to Paid Plans

### When to Upgrade

Consider upgrading if you need:
- Services that stay awake 24/7
- More RAM and CPU
- Automatic database backups
- Better performance
- Production reliability

### Upgrade Steps

1. Go to your service → **Settings**
2. Click **"Change Plan"**
3. Select a plan:
   - **Starter**: $7/month per service (good for small apps)
   - **Standard**: $25/month per service (recommended for production)
   - **Pro**: Custom pricing (for high traffic)

4. Database upgrades:
   - **Starter**: $7/month (1GB → 10GB, backups)
   - **Standard**: $20/month (10GB, better performance)

### Cost Estimation

**Minimum Production Setup:**
- Web Service: $7/month (Starter)
- Worker Service: $7/month (Starter)
- Database: $7/month (Starter)
- **Total: ~$21/month**

**Recommended Production Setup:**
- Web Service: $25/month (Standard)
- Worker Service: $7/month (Starter)
- Database: $20/month (Standard)
- **Total: ~$52/month**

---

## 📚 Additional Resources

### Render Documentation
- [Render Docs](https://render.com/docs)
- [Docker on Render](https://render.com/docs/docker)
- [Environment Variables](https://render.com/docs/environment-variables)
- [PostgreSQL on Render](https://render.com/docs/databases)

### Laravel Deployment
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Laravel Optimization](https://laravel.com/docs/optimization)

### Support
- [Render Community Forum](https://community.render.com)
- [Render Status Page](https://status.render.com)

---

## ✅ Deployment Checklist

Use this checklist to ensure everything is set up correctly:

### Pre-Deployment
- [ ] Code pushed to GitHub
- [ ] `render.yaml` exists and is correct
- [ ] `Dockerfile` exists
- [ ] `start.sh` is executable
- [ ] All dependencies in `composer.json` and `package.json`

### Deployment
- [ ] Blueprint created and applied
- [ ] All 3 services created (web, worker, database)
- [ ] Services are running

### Configuration
- [ ] `APP_KEY` generated and set
- [ ] `APP_URL` set to your Render URL
- [ ] `MAIL_USERNAME` set
- [ ] `MAIL_PASSWORD` set (Gmail app password)
- [ ] `MAIL_FROM_ADDRESS` set
- [ ] `OPENAI_API_KEY` set (if using AI features)

### Verification
- [ ] Application loads without errors
- [ ] Can login with default credentials
- [ ] Database migrations completed
- [ ] Storage link created
- [ ] Email sending works
- [ ] Queue jobs process
- [ ] File uploads work
- [ ] Changed default admin password

### Post-Deployment
- [ ] Custom domain configured (if needed)
- [ ] Auto-deploy enabled
- [ ] Monitoring set up
- [ ] Backups configured (if on paid plan)

---

## 🎉 Success!

Your Quiz LMS application should now be live on Render! 

**Next Steps:**
1. Test all features thoroughly
2. Set up monitoring and alerts
3. Configure backups
4. Share your application URL with users

**Need Help?**
- Check Render logs for errors
- Review this guide's troubleshooting section
- Visit Render community forum
- Check Laravel documentation

---

**Last Updated**: 2024
**Render Version**: Latest
**Laravel Version**: 11.x

