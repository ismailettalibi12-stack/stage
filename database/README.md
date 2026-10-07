# Database Setup Guide

## Files

- `schema.sql` - Complete database schema for the MVC Gestion application
- `setup.php` - Automated setup script to create all tables

## Automatic Setup (Recommended)

After deploying your app to Railway:

### Option 1: Use SSH
```bash
# SSH into your Railway container
railway run bash

# Navigate to the database directory
cd /app/database

# Run PHP setup script
php setup.php
```

### Option 2: Create an HTTP Endpoint
Create a route in your app that calls the setup script:

```php
<?php
// In your controller or a dedicated setup route
if ($_GET['setup'] === 'database' && $_ENV['SETUP_TOKEN'] === $_GET['token']) {
    include '../database/setup.php';
}
?>
```

Then visit: `https://your-app.railway.app/?setup=database&token=YOUR_SECRET_TOKEN`

## Manual Setup

If you prefer to set up the database manually:

1. Connect to Railway MySQL:
```bash
mysql -h mysql.railway.internal -u root -p
```

2. Run the schema:
```bash
mysql -h mysql.railway.internal -u root -p < schema.sql
```

Or paste the contents of `schema.sql` directly in your MySQL client.

## Database Structure

The schema creates these tables:

### Core Tables
- **users** - Application users (id, name, email, password, role)
- **patients** - Patient records (full_name, birth_date, phone, email)

### Academic Management
- **students** - Student records (first_name, last_name, major, level, email)
- **internships** - Internship data (company_name, type, dates, tech_stack)
- **validations** - Internship validations (defense_date, jury_members, final_grade, status)

### Medical/Healthcare
- **prescriptions** - Medication prescriptions (medication_name, dosage, frequency, dates)
- **prc** - PEC requests ("Prise en Charge") (Patient, Date, Organisme, statut)

### Inventory
- **stock** - Stock management (item_name, category, quantity, unit, expiry_date, supplier, unit_price)

## Environment Variables

The setup script uses these Railway environment variables:
- `DB_HOST` - Database hostname (default: localhost)
- `DB_PORT` - Database port (default: 3306)
- `DB_USER` - Database username (default: root)
- `DB_PASSWORD` - Database password (default: empty)

These are automatically set by Railway when you add the MySQL service.

## Notes

- All tables use `utf8mb4` charset for full Unicode support
- Foreign keys are enabled with CASCADE delete
- Indexes are created for common query columns
- Timestamps are automatically managed (created_at, updated_at)

