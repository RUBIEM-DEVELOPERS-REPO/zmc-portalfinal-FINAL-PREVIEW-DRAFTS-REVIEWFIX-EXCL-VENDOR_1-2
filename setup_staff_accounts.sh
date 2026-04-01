#!/bin/bash

# ZMC PORTAL - STAFF ACCOUNTS SETUP SCRIPT
# This script will populate the database with staff accounts

echo "🚀 ZMC PORTAL - STAFF ACCOUNTS SETUP"
echo "======================================"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the Laravel project root directory"
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found. Please create it first."
    exit 1
fi

echo "📋 This script will:"
echo "   1. Run database migrations"
echo "   2. Seed roles and permissions"
echo "   3. Create staff accounts"
echo "   4. Generate application key"
echo ""

read -p "Do you want to continue? (y/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Setup cancelled."
    exit 1
fi

echo ""
echo "🔄 Step 1: Running database migrations..."
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo "❌ Migration failed. Please check your database configuration."
    exit 1
fi

echo "✅ Migrations completed successfully."
echo ""

echo "🔄 Step 2: Seeding roles and permissions..."
php artisan db:seed --class=RolesAndPermissionsSeeder --force

if [ $? -ne 0 ]; then
    echo "❌ Roles seeding failed."
    exit 1
fi

echo "✅ Roles and permissions seeded successfully."
echo ""

echo "🔄 Step 3: Creating staff accounts..."
php artisan db:seed --class=StaffAccountsSeeder --force

if [ $? -ne 0 ]; then
    echo "❌ Staff accounts seeding failed."
    exit 1
fi

echo "✅ Staff accounts created successfully."
echo ""

echo "🔄 Step 4: Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application optimized."
echo ""

echo "🎉 SETUP COMPLETED SUCCESSFULLY!"
echo ""
echo "📱 Staff accounts are now ready for use."
echo ""
echo "🔐 Login Credentials Summary:"
echo "   • Admin: admin / ZMC@Admin2024!"
echo "   • Registrar: registrar / ZMC@Reg2024!"
echo "   • Accounts: accounts_officer / ZMC@Accnt2024!"
echo "   • Officer: accreditation_officer / ZMC@Acc2024!"
echo ""
echo "📚 For complete credentials, see:"
echo "   • STAFF_CREDENTIALS.md (detailed list)"
echo "   • QUICK_LOGIN_REFERENCE.md (quick reference)"
echo ""
echo "🌐 Login URL: https://portal.zmc.co.zw"
echo ""
echo "⚠️  IMPORTANT:"
echo "   • All staff must change passwords on first login"
echo "   • Keep credentials secure and confidential"
echo "   • Report any issues to IT support"
echo ""
echo "📞 Support Contacts:"
echo "   • IT Support: it.support@zmc.co.zw | +263 242 752 860"
echo "   • System Admin: admin@zmc.co.zw | +263 242 752 861"
echo ""

echo "✅ ZMC Portal staff setup is now complete!"
