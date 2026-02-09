# Customer Inquiry Management System

A Laravel 11 (LTS) based API for managing customer inquiries for a Stock Exchange website. This system handles inquiries related to Trading, Market Data, Technical Issues, and General Questions with comprehensive logging, validation, and transaction handling.

## Features

- ✅ RESTful API endpoints for inquiry management
- ✅ Comprehensive validation with custom error messages
- ✅ Database transaction handling for data integrity
- ✅ Soft deletes for audit trail
- ✅ Advanced filtering and search capabilities
- ✅ Pagination support
- ✅ Detailed logging for compliance and auditing
- ✅ Service layer for business logic separation
- ✅ Resource transformers for consistent API responses
- ✅ Error handling with rollback support

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher 
- Laravel 11.x (LTS)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd customer-inquiry-management
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file and configure your database:

```bash
cp .env.example .env
```

Update the following in your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_inquiry_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create Database

Create the database in MySQL:

```sql
CREATE DATABASE customer_inquiry_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. (Optional) Seed Sample Data

```bash
php artisan db:seed --class=InquirySeeder
```

### 8. Start the Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

## API Documentation

### Base URL

```
http://localhost:8000/api
```

### Endpoints

#### 1. Health Check

**GET** `/health`

Check if the API is running.

**Response:**
```json
{
  "success": true,
  "message": "API is running",
  "timestamp": "2024-02-09T06:00:00Z"
}
```

---

#### 2. Get All Inquiries

**GET** `/inquiries`

Retrieve a paginated list of inquiries with optional filtering.

**Query Parameters:**
- `category` (optional): Filter by category (Trading, Market Data, Technical Issues, General Questions)
- `status` (optional): Filter by status (pending, in_progress, resolved, closed)
- `priority` (optional): Filter by priority (low, medium, high, urgent)
- `search` (optional): Search in name, email, subject, or message
- `sort_by` (optional): Sort field (default: created_at)
- `sort_order` (optional): Sort order (asc, desc) (default: desc)
- `per_page` (optional): Items per page (1-100) (default: 15)

**Example Request:**
```bash
GET /api/inquiries?category=Trading&status=pending&per_page=20
```

**Response:**
```json
{
  "success": true,
  "message": "Inquiries retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "category": "Trading",
        "subject": "Question about stock trading",
        "message": "I need help understanding...",
        "status": "pending",
        "priority": "medium",
        "resolved_at": null,
        "resolution_notes": null,
        "created_at": "2024-02-09T06:00:00Z",
        "updated_at": "2024-02-09T06:00:00Z"
      }
    ],
    "meta": {
      "total": 100,
      "count": 20,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 5
    },
    "links": {
      "first": "http://localhost:8000/api/inquiries?page=1",
      "last": "http://localhost:8000/api/inquiries?page=5",
      "prev": null,
      "next": "http://localhost:8000/api/inquiries?page=2"
    }
  }
}
```

---

#### 3. Create New Inquiry

**POST** `/inquiries`

Submit a new customer inquiry.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "category": "Trading",
  "subject": "Question about stock trading",
  "message": "I need help understanding how to place a market order...",
  "priority": "medium"
}
```

**Required Fields:**
- `name` (string, max: 255)
- `email` (valid email, max: 255)
- `category` (enum: Trading, Market Data, Technical Issues, General Questions)
- `subject` (string, max: 255)
- `message` (string, min: 10)

**Optional Fields:**
- `phone` (string, max: 20)
- `priority` (enum: low, medium, high, urgent) (default: medium)

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Inquiry submitted successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "category": "Trading",
    "subject": "Question about stock trading",
    "message": "I need help understanding...",
    "status": "pending",
    "priority": "medium",
    "resolved_at": null,
    "resolution_notes": null,
    "created_at": "2024-02-09T06:00:00Z",
    "updated_at": "2024-02-09T06:00:00Z"
  }
}
```

**Validation Error Response (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Please provide a valid email address."],
    "category": ["The selected category is invalid."]
  }
}
```

---

#### 4. Get Single Inquiry

**GET** `/inquiries/{id}`

Retrieve a specific inquiry by ID.

**Example Request:**
```bash
GET /api/inquiries/1
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Inquiry retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "category": "Trading",
    "subject": "Question about stock trading",
    "message": "I need help understanding...",
    "status": "pending",
    "priority": "medium",
    "resolved_at": null,
    "resolution_notes": null,
    "created_at": "2024-02-09T06:00:00Z",
    "updated_at": "2024-02-09T06:00:00Z"
  }
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Inquiry not found",
  "error": "No inquiry found with ID: 999"
}
```

---

#### 5. Update Inquiry

**PUT/PATCH** `/inquiries/{id}`

Update an existing inquiry (typically used by support staff).

**Request Body:**
```json
{
  "status": "in_progress",
  "priority": "high",
  "resolution_notes": "Working on this issue..."
}
```

**Updatable Fields:**
- `name` (string, max: 255)
- `email` (valid email, max: 255)
- `phone` (string, max: 20)
- `category` (enum)
- `subject` (string, max: 255)
- `message` (string, min: 10)
- `status` (enum: pending, in_progress, resolved, closed)
- `priority` (enum: low, medium, high, urgent)
- `resolution_notes` (string)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Inquiry updated successfully",
  "data": {
    "id": 1,
    "status": "in_progress",
    "priority": "high",
    "resolution_notes": "Working on this issue...",
    ...
  }
}
```

---

#### 6. Delete Inquiry

**DELETE** `/inquiries/{id}`

Soft delete an inquiry (maintains audit trail).

**Example Request:**
```bash
DELETE /api/inquiries/1
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Inquiry deleted successfully"
}
```

---

#### 7. Get Statistics

**GET** `/inquiries/statistics`

Retrieve inquiry statistics grouped by status, category, and priority.

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Statistics retrieved successfully",
  "data": {
    "total": 150,
    "by_status": {
      "pending": 45,
      "in_progress": 30,
      "resolved": 60,
      "closed": 15
    },
    "by_category": {
      "Trading": 50,
      "Market Data": 40,
      "Technical Issues": 35,
      "General Questions": 25
    },
    "by_priority": {
      "low": 30,
      "medium": 70,
      "high": 40,
      "urgent": 10
    }
  }
}
```

---

## Error Handling

All endpoints follow a consistent error response format:

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to create inquiry",
  "error": "Database connection failed"
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Please provide a valid email address."],
    "message": ["The message must be at least 10 characters long."]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Inquiry not found",
  "error": "No inquiry found with ID: 999"
}
```

## Database Schema

### Inquiries Table

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR(255) | Customer name |
| email | VARCHAR(255) | Customer email |
| phone | VARCHAR(20) | Customer phone (optional) |
| category | ENUM | Trading, Market Data, Technical Issues, General Questions |
| subject | VARCHAR(255) | Inquiry subject |
| message | TEXT | Inquiry message |
| status | ENUM | pending, in_progress, resolved, closed |
| priority | ENUM | low, medium, high, urgent |
| resolved_at | TIMESTAMP | Resolution timestamp |
| resolution_notes | TEXT | Resolution notes |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |
| deleted_at | TIMESTAMP | Soft delete timestamp |

**Indexes:**
- category
- status
- priority
- created_at

## Architecture

### Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── InquiryController.php
│   ├── Requests/
│   │   ├── StoreInquiryRequest.php
│   │   └── UpdateInquiryRequest.php
│   └── Resources/
│       ├── InquiryResource.php
│       └── InquiryCollection.php
├── Models/
│   └── Inquiry.php
└── Services/
    └── InquiryService.php
```

### Design Patterns

1. **Service Layer Pattern**: Business logic is separated into `InquiryService` for better maintainability and testability.

2. **Repository Pattern**: Eloquent ORM acts as the repository layer with custom query scopes.

3. **Form Request Validation**: Dedicated request classes (`StoreInquiryRequest`, `UpdateInquiryRequest`) handle validation logic.

4. **Resource Transformers**: API responses are transformed using Laravel Resources for consistent output.

5. **Transaction Management**: All write operations are wrapped in database transactions for data integrity.

## Logging

All operations are logged with relevant context:

- **Inquiry Creation**: Logs inquiry ID, category, and email
- **Inquiry Updates**: Logs updated fields and status changes
- **Inquiry Deletion**: Logs deleted inquiry details
- **Errors**: Comprehensive error logging with stack traces

Logs are stored in `storage/logs/laravel.log`

## Testing

Run the test suite:

```bash
php artisan test
```

## Security Considerations

- ✅ SQL Injection protection via Eloquent ORM
- ✅ Mass assignment protection via `$fillable` property
- ✅ Input validation on all endpoints
- ✅ Soft deletes for audit trail
- ✅ Comprehensive error logging
- ✅ Database transactions for data integrity

## Production Deployment

Before deploying to production:

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Set up proper database backups
7. Configure log rotation
8. Set up monitoring and alerting

## Support

For issues or questions, please contact the development team.

## License

This project is proprietary software developed for Stock Exchange customer inquiry management.
