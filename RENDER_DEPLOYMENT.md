# Render Deployment Guide

## Build Configuration

### Option 1: Using the Build Script (Recommended)

Update your Render build command to:
```bash
./build.sh
```

This script ensures the correct build order:
1. Install Composer dependencies (including Ziggy)
2. Install Node dependencies
3. Build frontend assets

### Option 2: Manual Build Command

If you prefer not to use the build script, use this build command:
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev && npm install && npm run build
```

## Why This Is Needed

The application uses Laravel Ziggy for route generation, which is a PHP package installed via Composer. The frontend build process needs access to Ziggy's JavaScript files located in `vendor/tightenco/ziggy`.

The Vite configuration includes a fallback mechanism that allows the build to succeed even if the vendor directory doesn't exist yet, but for production deployments, it's better to ensure Composer dependencies are installed first.

## Vite Configuration

The `vite.config.ts` includes:
- A check for the vendor directory existence
- A custom Vite plugin that provides a fallback implementation when Ziggy is not available
- Conditional alias resolution based on vendor directory availability

This ensures the build can succeed in various environments while maintaining full functionality when all dependencies are properly installed.

## Environment Variables

Make sure to set all required environment variables in your Render dashboard:
- `APP_KEY`
- `APP_URL`
- Database credentials
- GIRAS integration credentials
- Social media API keys (if using social media auto-posting)
- Any other application-specific variables

## Post-Deployment

After deployment, run:
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

These commands are typically added to the Render "Start Command" or as a post-deploy hook.

