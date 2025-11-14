# 🚀 Deployment Guide for Render.com

This guide will help you deploy your Laravel LMS application to Render.com using Docker.

## 📋 Files Created

1. **Dockerfile** - Docker configuration for building your application
2. **.dockerignore** - Files to exclude from Docker build
3. **render.yaml** - Render service configuration (updated for Docker)

## 🎯 Quick Deployment Steps

### 1. Push to GitHub

Make sure all files are committed and pushed:

```bash
git add .
git commit -m "Add Docker configuration for Render deployment"
git push origin main
```

### 2. Deploy on Render

1. Go to https://dashboard.render.com
2. Click **"New +"** → **"Blueprint"**
3. Connect your GitHub repository
4. Render will automatically detect `render.yaml` and set up:
   - Web service (quiz-lms-web)
   - Worker service (quiz-lms-worker)
   - PostgreSQL database (quiz-lms-db)
5. Click **"Apply"** to deploy

### 3. Configure Environment Variables

After the first deployment, go to your **web service** → **"Environment"** and set:

#### Required Variables:
- `APP_KEY` - Generate in shell: `php artisan key:generate`
- `APP_URL` - Your Render URL (e.g., `https://quiz-lms-web.onrender.com`)

#### Email Configuration:
- `MAIL_USERNAME` - Your Gmail address
- `MAIL_PASSWORD` - Gmail app password (not your regular password)
- `MAIL_FROM_ADDRESS` - Your email address

#### OpenAI (if using AI features):
- `OPENAI_API_KEY` - Your OpenAI API key

### 4. Run Initial Setup

Once deployed, use the **Shell** tab in your web service:

```bash
# Generate application key (if not set)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force

# Create storage link
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Verify Deployment

1. Visit your app URL: `https://your-app-name.onrender.com`
2. Test login with default credentials:
   - Username: `admin`
   - Password: `password`
3. **⚠️ IMPORTANT**: Change the default password immediately!

## 🔧 Configuration Details

### Database
- **Type**: PostgreSQL (free tier)
- **Connection**: Automatically configured via `render.yaml`
- **Credentials**: Auto-linked from database service

### Services
- **Web Service**: Serves your Laravel application
- **Worker Service**: Processes queue jobs (emails, notifications, etc.)
- **Database**: PostgreSQL database

## 📝 Environment Variables Reference

All environment variables are configured in `render.yaml`. Variables marked with `sync: false` need to be set manually in Render dashboard:

- `APP_KEY` - Application encryption key
- `APP_URL` - Your application URL
- `MAIL_USERNAME` - Email username
- `MAIL_PASSWORD` - Email password
- `MAIL_FROM_ADDRESS` - Email from address
- `OPENAI_API_KEY` - OpenAI API key

## ⚠️ Important Notes

### Free Tier Limitations:
- Services spin down after 15 minutes of inactivity
- First request after spin-down may take 30-60 seconds
- 750 hours/month total runtime
- Database: 1GB storage, 90-day retention

### Performance Tips:
1. Enable auto-deploy only for main branch
2. Cache config, routes, and views (already in setup)
3. Consider paid plan for production use

## 🐛 Troubleshooting

### App shows 500 error:
- Check logs: Service → "Logs"
- Verify `APP_KEY` is set
- Ensure database credentials are correct
- Check file permissions

### Database connection fails:
- Verify database is running
- Check internal database URL
- Ensure firewall allows connections

### Assets not loading:
- Verify `npm run build` completed
- Check `public/build` exists
- Clear browser cache

### Queue jobs not running:
- Ensure background worker is running
- Check `QUEUE_CONNECTION=database`
- Verify worker has same env vars

## 📚 Additional Resources

- [Render Documentation](https://render.com/docs)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Docker Documentation](https://docs.docker.com/)

## ✅ Post-Deployment Checklist

- [ ] App loads without errors
- [ ] Database migrations completed
- [ ] Default admin login works
- [ ] File uploads work (check storage)
- [ ] Email sending works (test notification)
- [ ] Queue jobs process (if using)
- [ ] Assets load correctly
- [ ] SSL certificate active (automatic on Render)
- [ ] Changed default password
- [ ] Production optimizations applied

---

**Need Help?** Check Render's logs or community forums for assistance.

