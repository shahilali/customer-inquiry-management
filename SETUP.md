# Quick Setup Guide

## Prerequisites

Ensure you have the following installed:
- PHP 8.2+
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Git

## Quick Start (Windows with Laragon)

If you're using Laragon on Windows:

1. **Place the project in Laragon's www directory**
   ```
   C:\laragon\www\customer-inquiry-management
   ```

2. **Open Laragon Terminal and navigate to project**
   ```bash
   cd C:\laragon\www\customer-inquiry-management
   ```

3. **Install dependencies**
   ```bash
   composer install
   ```

4. **Configure environment**
   ```bash
   copy .env.example .env
   ```

5. **Edit `.env` file** with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=customer_inquiry_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Generate application key**
   ```bash
   php artisan key:generate
   ```

7. **Create database** (via Laragon MySQL or phpMyAdmin)
   ```sql
   CREATE DATABASE customer_inquiry_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

8. **Run migrations**
   ```bash
   php artisan migrate
   ```

9. **Seed sample data (optional)**
   ```bash
   php artisan db:seed --class=InquirySeeder
   ```

10. **Start the server**
    ```bash
    php artisan serve
    ```

11. **Test the API**
    - Health check: http://localhost:8000/api/health
    - Get inquiries: http://localhost:8000/api/inquiries
    - Import `postman_collection.json` into Postman for full API testing

## Quick Start (Linux/Mac)

1. **Clone and navigate**
   ```bash
   git clone <repository-url>
   cd customer-inquiry-management
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```

4. **Edit `.env` file** with your database credentials

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Create database**
   ```bash
   mysql -u root -p
   CREATE DATABASE customer_inquiry_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   exit;
   ```

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Seed sample data (optional)**
   ```bash
   php artisan db:seed --class=InquirySeeder
   ```

9. **Start the server**
   ```bash
   php artisan serve
   ```

10. **Test the API**
    - Health check: http://localhost:8000/api/health
    - Get inquiries: http://localhost:8000/api/inquiries

## Testing with cURL

### Health Check
```bash
curl http://localhost:8000/api/health
```

### Create an Inquiry
```bash
curl -X POST http://localhost:8000/api/inquiries \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "phone": "+1234567890",
    "category": "Trading",
    "subject": "Test Inquiry",
    "message": "This is a test inquiry message with sufficient length.",
    "priority": "medium"
  }'
```

### Get All Inquiries
```bash
curl http://localhost:8000/api/inquiries
```

### Get Single Inquiry
```bash
curl http://localhost:8000/api/inquiries/1
```

### Update Inquiry
```bash
curl -X PUT http://localhost:8000/api/inquiries/1 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in_progress",
    "priority": "high"
  }'
```

### Delete Inquiry
```bash
curl -X DELETE http://localhost:8000/api/inquiries/1
```

### Get Statistics
```bash
curl http://localhost:8000/api/inquiries/statistics
```

## Troubleshooting

### Issue: "could not find driver"
**Solution**: Enable PDO MySQL extension in `php.ini`
```ini
extension=pdo_mysql
```

### Issue: "Access denied for user"
**Solution**: Check your database credentials in `.env` file

### Issue: "Base table or view not found"
**Solution**: Run migrations
```bash
php artisan migrate
```

### Issue: "Permission denied" on storage
**Solution**: Set proper permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: API routes not working
**Solution**: Clear route cache
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## Verification Checklist

- [ ] Dependencies installed (`vendor/` directory exists)
- [ ] `.env` file configured with correct database credentials
- [ ] Application key generated (check `APP_KEY` in `.env`)
- [ ] Database created
- [ ] Migrations run successfully
- [ ] API health check returns success
- [ ] Can create a new inquiry via API
- [ ] Can retrieve inquiries via API

## Next Steps

1. Review the full API documentation in `README.md`
2. Import `postman_collection.json` into Postman for comprehensive testing
3. Review the code structure in the Architecture section of README
4. Set up proper logging and monitoring for production use

## Support

For detailed API documentation, see `README.md`
For issues, check the Troubleshooting section above
