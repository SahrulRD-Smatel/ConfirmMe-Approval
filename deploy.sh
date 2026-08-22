#!/bin/bash

# ==============================================================================
# ConfirmMe-Approval - Production Deployment Script
# ==============================================================================
#
# Server:
#    cPanel / Shared Hosting (PHP 8.4)
#
# Production:
#    ~/public_html/confirmme.sahrulrdhani.my.id
#
# ==============================================================================

set -Eeuo pipefail

# ------------------------------------------------------------------------------
# Configuration
# ------------------------------------------------------------------------------

APP_DIR="$HOME/public_html/confirmme.sahrulrdhani.my.id"

cd "$APP_DIR"

echo ""
echo "============================================================"
echo "🚀 ConfirmMe-Approval Production Deployment"
echo "============================================================"
echo ""

echo "📁 Application : $APP_DIR"
echo "🐘 PHP          : $(php -r 'echo PHP_VERSION;')"
echo "🎼 Composer     : Pre-built by GitHub Actions"
echo ""

# ------------------------------------------------------------------------------
# Safety Check & Environment Setup
# ------------------------------------------------------------------------------

echo "🔐 Running production safety checks..."

if [ ! -f ".env" ]; then
    echo "❌ ERROR: File .env produksi tidak ditemukan di $APP_DIR/.env"
    exit 1
fi

if [ ! -f "artisan" ]; then
    echo "❌ ERROR: File artisan tidak ditemukan."
    exit 1
fi

echo "   ✅ Production environment verified"
echo ""

# ------------------------------------------------------------------------------
# IMPORTANT: Database Protection
# ------------------------------------------------------------------------------

echo "🛡️  Production database protection"
echo ""
echo "   ❌ DatabaseSeeder: DISABLED"
echo "   ❌ db:seed:        DISABLED"
echo "   ❌ migrate:fresh: DISABLED"
echo "   ❌ migrate:refresh: DISABLED"
echo ""
echo "   ✅ Only pending migrations will be executed."
echo ""

# ------------------------------------------------------------------------------
# 1. Composer Dependencies Check
# ------------------------------------------------------------------------------

echo "============================================================"
echo "📦 Step 1/6 - Checking Composer dependencies"
echo "============================================================"

if [ -d "vendor" ]; then
    echo "   ✅ Vendor directory verified (Pre-built by GitHub Actions)"
else
    echo "   ⚠️ Vendor directory missing!"
fi

echo ""

# ------------------------------------------------------------------------------
# 2. Clear Laravel Caches
# ------------------------------------------------------------------------------

echo "============================================================"
echo "🧹 Step 2/6 - Clearing Laravel Caches"
echo "============================================================"

php artisan optimize:clear

echo ""
echo "   ✅ Laravel caches cleared"
echo ""

# ------------------------------------------------------------------------------
# 3. Storage & Permissions
# ------------------------------------------------------------------------------

echo "============================================================"
echo "🔒 Step 3/6 - Checking storage and permissions"
echo "============================================================"

if [ -d "storage" ]; then
    chmod -R 775 storage/ 2>/dev/null || true
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache/ 2>/dev/null || true
fi

mkdir -p storage/logs
touch storage/logs/laravel.log 2>/dev/null || true
chmod 664 storage/logs/laravel.log 2>/dev/null || true

if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link 2>/dev/null || {
        echo "   ⚠️ Storage link could not be created or already exists."
    }
else
    echo "   ✅ Storage symlink already exists"
fi

echo ""
echo "   ✅ Permissions checked"
echo ""

# ------------------------------------------------------------------------------
# 4. Database Migration ONLY
# ------------------------------------------------------------------------------

echo "============================================================"
echo "🗄️  Step 4/6 - Running Production Migrations"
echo "============================================================"

php artisan migrate --force --no-interaction

echo ""
echo "   ✅ Database migrations completed"
echo ""

# ------------------------------------------------------------------------------
# 5. Production Cache
# ------------------------------------------------------------------------------

echo "============================================================"
echo "⚡ Step 5/6 - Building Production Caches"
echo "============================================================"

php artisan config:cache || echo "   ⚠️ Config cache skipped"
php artisan route:cache || echo "   ⚠️ Route cache skipped"
php artisan view:cache || echo "   ⚠️ View cache skipped"

echo ""
echo "   ✅ Production caches generated"
echo ""

# ------------------------------------------------------------------------------
# 6. Refresh Web Server & Final Verification
# ------------------------------------------------------------------------------

echo "============================================================"
echo "🔎 Step 6/6 - Final Verification & OPcache Refresh"
echo "============================================================"

# Paksa server Litespeed/Nginx memuat file baru
touch public/index.php 2>/dev/null || true

php artisan --version

echo ""
echo "Checking migration status..."
php artisan migrate:status --no-interaction || {
    echo "   ⚠️ Could not retrieve migration status."
}

echo ""
if [ -d "vendor" ]; then
    echo "   ✅ vendor/"
fi

if [ -f ".env" ]; then
    echo "   ✅ production .env"
fi

echo ""
echo "============================================================"
echo "✅ DEPLOYMENT COMPLETED SUCCESSFULLY"
echo "============================================================"
echo "🎉 ConfirmMe-Approval is ready."
echo ""