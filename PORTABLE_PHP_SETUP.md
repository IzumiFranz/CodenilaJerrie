# Portable PHP Setup (No Installation Required)

## Quick Setup - 3 Steps

### Step 1: Download Portable PHP

1. Go to: https://windows.php.net/download/
2. Download: **PHP 8.2 Thread Safe ZIP** (e.g., `php-8.2.x-Win32-vs16-x64.zip`)
3. Extract the ZIP file to: `C:\Users\User\PHP\php-portable\`
   - You should have: `C:\Users\User\PHP\php-portable\php.exe`

### Step 2: Download Portable Composer

1. Go to: https://getcomposer.org/download/
2. Download: **composer.phar** (the standalone file)
3. Save it to: `C:\Users\User\PHP\php-portable\composer.phar`

### Step 3: Use the Helper Scripts

I've created helper scripts (`run-php.bat` and `run-artisan.bat`) that you can use to run PHP commands without adding anything to PATH.

## Usage

After setup, you can run:

```powershell
# Run artisan commands
.\run-artisan.bat key:generate

# Or any other artisan command
.\run-artisan.bat migrate
.\run-artisan.bat --version
```

That's it! No installation, no PATH changes needed.

