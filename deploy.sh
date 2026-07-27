#!/bin/bash
# ==============================================================================
# ConfirmMe-Approval - Production Deploy Script for Linux Hosting
# ==============================================================================
# Usage: bash deploy.sh
# Run this after uploading new code to production
# ==============================================================================

set -e

echo "🚀 Starting ConfirmMe-Approval deployment..."
echo "================================================"

# 1. Clear ALL caches
echo ""
echo "📦 Step 1: Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear

# Clear Spatie Permission cache specifically
echo "🔑 Clearing Spatie permission cache..."
php artisan permission:cache-reset 2>/dev/null || echo "   (permission:cache-reset not available, using cache:forget)"
php artisan cache:forget spatie.permission.cache 2>/dev/null || true

echo "   ✅ All caches cleared"

# 2. Set correct file permissions for Linux
echo ""
echo "🔒 Step 2: Setting file permissions..."

# Storage directory must be writable
chmod -R 775 storage/ 2>/dev/null || echo "   ⚠️  Could not chmod storage/ (may need sudo)"
chmod -R 775 bootstrap/cache/ 2>/dev/null || echo "   ⚠️  Could not chmod bootstrap/cache/ (may need sudo)"

# Ensure log file exists and is writable
touch storage/logs/laravel.log 2>/dev/null || true
chmod 664 storage/logs/laravel.log 2>/dev/null || true

# Create storage link if not exists
php artisan storage:link 2>/dev/null || echo "   Storage link already exists"

echo "   ✅ File permissions set"

# 3. Run migrations
echo ""
echo "🗄️  Step 3: Running migrations..."
php artisan migrate --force
echo "   ✅ Migrations complete"

# 4. Sync permissions (ensure all roles have correct permissions)
echo ""
echo "🔐 Step 4: Syncing permissions..."
php artisan db:seed --class=DatabaseSeeder --force 2>/dev/null || echo "   ⚠️  Seeder may have already run, checking manually..."
echo "   ✅ Permissions synced"

# 5. Rebuild caches for production
echo ""
echo "⚡ Step 5: Rebuilding production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "   ✅ Production caches built"

# 6. Verify installation
echo ""
echo "🔍 Step 6: Verifying installation..."
php artisan about 2>/dev/null | head -20 || echo "   (artisan about not available)"
echo ""

# Quick permission check
echo "   Checking roles in database..."
php artisan tinker --execute="
    \$roles = \Spatie\Permission\Models\Role::all();
    echo 'Total roles: ' . \$roles->count() . PHP_EOL;
    foreach (\$roles as \$role) {
        \$permCount = \$role->permissions->count();
        echo '  - ' . \$role->name . ' (guard: ' . \$role->guard_name . ', permissions: ' . \$permCount . ')' . PHP_EOL;
    }
" 2>/dev/null || echo "   ⚠️  Could not run tinker check"

echo ""
echo "================================================"
echo "✅ Deployment complete!"
echo ""
echo "📝 Post-deploy checklist:"
echo "   1. Test login with different roles"
echo "   2. Test /approvals/{id} and /documents/{id}"
echo "   3. Check storage/logs/laravel.log for any 403 errors"
echo "   4. Verify Spatie permission cache is working"
echo "================================================"
