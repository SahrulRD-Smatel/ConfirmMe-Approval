#!/bin/bash

# ==============================================================================
# ConfirmMe-Approval - Production Deployment Script
# ==============================================================================
#
# IMPORTANT:
# - JANGAN menjalankan DatabaseSeeder pada production.
# - Database production berisi data real.
# - Script ini TIDAK melakukan db:seed.
# - Script ini TIDAK melakukan migrate:fresh.
# - Script ini TIDAK melakukan migrate:refresh.
# - Migration production hanya dijalankan menggunakan:
#        php artisan migrate --force
#
# Server:
#    cPanel / Shared Hosting
#    PHP 8.4
#
# Repository:
#    ~/repositories/ConfirmMe-Approval
#
# Production:
#    ~/public_html/confirmme.sahrulrdhani.my.id
#
# ==============================================================================

set -Eeuo pipefail

# ------------------------------------------------------------------------------
# Configuration
# ------------------------------------------------------------------------------

APP_DIR="$HOME/repositories/ConfirmMe-Approval"
PUBLIC_DIR="$HOME/public_html/confirmme.sahrulrdhani.my.id"

cd "$APP_DIR"

echo ""
echo "============================================================"
echo "🚀 ConfirmMe-Approval Production Deployment"
echo "============================================================"
echo ""

echo "📁 Application : $APP_DIR"
echo "🌐 Production  : $PUBLIC_DIR"
echo "🐘 PHP          : $(php -r 'echo PHP_VERSION;')"
echo "🎼 Composer     : Pre-built by GitHub Actions"
echo ""

# ------------------------------------------------------------------------------
# Safety Check
# ------------------------------------------------------------------------------

echo "🔐 Running production safety checks..."

if [ ! -f ".env" ]; then
    echo "⚠️  .env tidak ditemukan di repository."
    echo "   Pastikan production menggunakan:"
    echo "   $PUBLIC_DIR/.env"
fi

if [ ! -f "$PUBLIC_DIR/.env" ]; then
    echo "❌ ERROR: Production .env tidak ditemukan:"
    echo "   $PUBLIC_DIR/.env"
    exit 1
fi

if [ ! -f "artisan" ]; then
    echo "❌ ERROR: artisan tidak ditemukan."
    exit 1
fi

if [ ! -d "$PUBLIC_DIR/public" ]; then
    echo "❌ ERROR: Production public directory tidak ditemukan:"
    echo "   $PUBLIC_DIR/public"
    exit 1
fi

echo "   ✅ Production environment detected"
echo ""

# ------------------------------------------------------------------------------
# IMPORTANT: Never run DatabaseSeeder
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
# 1. Composer Dependencies
# ------------------------------------------------------------------------------

echo "============================================================"
echo "📦 Step 1/7 - Checking Composer dependencies"
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
echo "🧹 Step 2/7 - Clearing Laravel caches"
echo "============================================================"

php artisan optimize:clear

echo ""
echo "   ✅ Laravel caches cleared"
echo ""

# ------------------------------------------------------------------------------
# 3. Storage & Permissions
# ------------------------------------------------------------------------------

echo "============================================================"
echo "🔒 Step 3/7 - Checking storage and permissions"
echo "============================================================"

# Laravel storage
if [ -d "storage" ]; then
    chmod -R 775 storage/ 2>/dev/null || true
fi

# Laravel bootstrap cache
if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache/ 2>/dev/null || true
fi

# Make sure Laravel log exists
mkdir -p storage/logs

touch storage/logs/laravel.log 2>/dev/null || true

chmod 664 storage/logs/laravel.log 2>/dev/null || true

# Create storage symlink if needed
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage symlink..."

    php artisan storage:link 2>/dev/null || {
        echo "   ⚠️ Storage link could not be created."
        echo "   It may already exist or require manual configuration."
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
echo "🗄️  Step 4/7 - Running production migrations"
echo "============================================================"

echo ""
echo "⚠️  Running:"
echo "   php artisan migrate --force"
echo ""
echo "IMPORTANT:"
echo "   - This does NOT run DatabaseSeeder."
echo "   - This does NOT reset the database."
echo "   - This only applies pending migration files."
echo ""

php artisan migrate --force --no-interaction

echo ""
echo "   ✅ Database migrations completed"
echo ""

# ------------------------------------------------------------------------------
# 5. Verify Database
# ------------------------------------------------------------------------------

echo "============================================================"
echo "🔍 Step 5/7 - Verifying application"
echo "============================================================"

php artisan about 2>/dev/null | head -30 || {
    echo "   ⚠️ artisan about tidak tersedia."
}

echo ""

# ------------------------------------------------------------------------------
# 6. Production Cache
# ------------------------------------------------------------------------------

echo "============================================================"
echo "⚡ Step 6/7 - Building production caches"
echo "============================================================"

php artisan config:cache

php artisan route:cache

php artisan view:cache

echo ""
echo "   ✅ Production caches generated"
echo ""

# ------------------------------------------------------------------------------
# 7. Final Verification
# ------------------------------------------------------------------------------

echo "============================================================"
echo "🔎 Step 7/7 - Final verification"
echo "============================================================"

echo ""
echo "Checking Laravel installation..."

php artisan --version

echo ""
echo "Checking migration status..."

php artisan migrate:status --no-interaction || {
    echo "   ⚠️ Could not retrieve migration status."
}

echo ""
echo "Checking important directories..."

if [ -d "vendor" ]; then
    echo "   ✅ vendor/"
else
    echo "   ❌ vendor/ missing"
    exit 1
fi

if [ -d "public/build" ]; then
    echo "   ✅ public/build/"
else
    echo "   ⚠️ public/build/ tidak ditemukan."
    echo "      Pastikan Vite sudah di-build oleh GitHub Actions."
fi

if [ -f "$PUBLIC_DIR/.env" ]; then
    echo "   ✅ production .env"
else
    echo "   ❌ production .env missing"
    exit 1
fi

echo ""

# ------------------------------------------------------------------------------
# Final
# ------------------------------------------------------------------------------

echo "============================================================"
echo "✅ DEPLOYMENT COMPLETED SUCCESSFULLY"
echo "============================================================"
echo ""

echo "Production application:"
echo "  $PUBLIC_DIR"

echo ""
echo "Database:"
echo "  ✅ Pending migrations only"
echo "  ❌ DatabaseSeeder NOT executed"
echo "  ❌ migrate:fresh NOT executed"
echo "  ❌ migrate:refresh NOT executed"

echo ""
echo "Cache:"
echo "  ✅ Config cache"
echo "  ✅ Route cache"
echo "  ✅ View cache"

echo ""
echo "Frontend:"
echo "  ℹ️ Node/npm tidak tersedia di cPanel."
echo "  ℹ️ Vite build harus dilakukan oleh GitHub Actions."
echo ""

echo "============================================================"
echo "🎉 ConfirmMe-Approval is ready."
echo "============================================================"
echo ""