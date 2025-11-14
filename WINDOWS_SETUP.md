# Windows PHP Setup Guide

## Quick Setup: Install PHP 8.2 on Windows

### Method 1: Using XAMPP (Easiest - Recommended for Beginners)

1. **Download XAMPP**
   - Go to https://www.apachefriends.org/download.html
   - Download XAMPP for Windows (includes PHP 8.2)
   - Run the installer

2. **Add PHP to PATH**
   - XAMPP installs PHP to `C:\xampp\php` (default location)
   - Add this to your Windows PATH:
     - Press `Win + X` and select "System"
     - Click "Advanced system settings"
     - Click "Environment Variables"
     - Under "System variables", find and select "Path", then click "Edit"
     - Click "New" and add: `C:\xampp\php`
     - Click "OK" on all dialogs

3. **Restart PowerShell** and test:
   ```powershell
   php -v
   ```

### Method 2: Manual PHP Installation (More Control)

1. **Download PHP**
   - Go to https://windows.php.net/download/
   - Download PHP 8.2 Thread Safe ZIP package
   - Extract to `C:\php` (or your preferred location)

2. **Add PHP to PATH**
   - Follow the same PATH steps as Method 1
   - Add `C:\php` to your PATH

3. **Configure PHP**
   - Copy `php.ini-development` to `php.ini` in your PHP directory
   - Edit `php.ini` and uncomment these extensions (remove the `;`):
     ```
     extension=mbstring
     extension=openssl
     extension=pdo_mysql
     extension=curl
     extension=fileinfo
     extension=gd
     extension=zip
     ```

4. **Restart PowerShell** and test:
   ```powershell
   php -v
   ```

### Method 3: Using Chocolatey (If you have it installed)

```powershell
choco install php -y
```

### Install Composer

After PHP is installed, you need Composer:

1. **Download Composer**
   - Go to https://getcomposer.org/download/
   - Download `Composer-Setup.exe`
   - Run the installer (it will detect PHP automatically)

2. **Verify installation**:
   ```powershell
   composer --version
   ```

### After Installation

Once PHP and Composer are installed, you can run:

```powershell
# Navigate to your project
cd C:\Users\User\PHP\quiz-lms-system

# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations (if needed)
php artisan migrate
```

### Troubleshooting

- **"php is not recognized"**: Make sure you restarted PowerShell after adding PHP to PATH
- **Extension errors**: Make sure required PHP extensions are enabled in `php.ini`
- **Composer errors**: Make sure PHP is in your PATH before installing Composer

