# Project Structure

## Overview

This document provides a detailed overview of the Customer Inquiry Management system architecture and file organization.

## Directory Structure

```
customer-inquiry-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── InquiryController.php          # Main API controller
│   │   ├── Requests/
│   │   │   ├── StoreInquiryRequest.php           # Validation for creating inquiries
│   │   │   └── UpdateInquiryRequest.php          # Validation for updating inquiries
│   │   └── Resources/
│   │       ├── InquiryResource.php               # Single inquiry response transformer
│   │       └── InquiryCollection.php             # Collection response transformer
│   ├── Models/
│   │   └── Inquiry.php                           # Inquiry Eloquent model
│   └── Services/
│       └── InquiryService.php                    # Business logic layer
├── bootstrap/
│   └── app.php                                   # Application bootstrap (API routes registered)
├── config/                                       # Laravel configuration files
├── database/
│   ├── factories/
│   │   └── InquiryFactory.php                    # Factory for testing
│   ├── migrations/
│   │   └── 2024_02_09_000000_create_inquiries_table.php
│   └── seeders/
│       └── InquirySeeder.php                     # Sample data seeder
├── routes/
│   ├── api.php                                   # API routes definition
│   ├── web.php                                   # Web routes (default Laravel)
│   └── console.php                               # Console commands
├── tests/
│   └── Feature/
│       └── InquiryApiTest.php                    # Comprehensive API tests
├── .env.example                                  # Environment configuration template
├── composer.json                                 # PHP dependencies
├── postman_collection.json                       # Postman API collection
├── PROJECT_STRUCTURE.md                          # This file
├── README.md                                     # Main documentation
└── SETUP.md                                      # Quick setup guide

```

## Core Components

### 1. Models (`app/Models/`)

**Inquiry.php**
- Eloquent model representing customer inquiries
- Defines fillable fields, casts, and relationships
- Contains query scopes for filtering (byCategory, byStatus, byPriority)
- Helper methods (markAsResolved, isPending, isResolved)
- Soft delete enabled for audit trail

### 2. Controllers (`app/Http/Controllers/Api/`)

**InquiryController.php**
- Handles all HTTP requests for inquiry management
- Methods:
  - `index()` - List all inquiries with filtering and pagination
  - `store()` - Create new inquiry
  - `show()` - Get single inquiry
  - `update()` - Update existing inquiry
  - `destroy()` - Soft delete inquiry
  - `statistics()` - Get inquiry statistics
- Comprehensive error handling
- Returns consistent JSON responses

### 3. Request Validation (`app/Http/Requests/`)

**StoreInquiryRequest.php**
- Validates data for creating new inquiries
- Custom validation messages
- Required fields: name, email, category, subject, message
- Optional fields: phone, priority

**UpdateInquiryRequest.php**
- Validates data for updating inquiries
- All fields optional (partial updates supported)
- Includes status and resolution_notes fields

### 4. Resources (`app/Http/Resources/`)

**InquiryResource.php**
- Transforms single inquiry model to JSON
- Formats dates to ISO 8601 standard
- Hides sensitive/internal fields

**InquiryCollection.php**
- Transforms paginated inquiry collection
- Includes metadata (total, count, per_page, etc.)
- Includes pagination links

### 5. Services (`app/Services/`)

**InquiryService.php**
- Business logic layer separating concerns from controller
- Database transaction handling
- Comprehensive logging for all operations
- Methods:
  - `getAllInquiries()` - Retrieve with filtering
  - `getInquiryById()` - Get single inquiry
  - `createInquiry()` - Create with transaction
  - `updateInquiry()` - Update with transaction
  - `deleteInquiry()` - Soft delete with transaction
  - `getStatistics()` - Calculate statistics

### 6. Routes (`routes/api.php`)

All routes are prefixed with `/api`:

```
GET    /api/health                    # Health check
GET    /api/inquiries                 # List inquiries
POST   /api/inquiries                 # Create inquiry
GET    /api/inquiries/statistics      # Get statistics
GET    /api/inquiries/{id}            # Get single inquiry
PUT    /api/inquiries/{id}            # Update inquiry
PATCH  /api/inquiries/{id}            # Partial update
DELETE /api/inquiries/{id}            # Delete inquiry
```

### 7. Database

**Migration: create_inquiries_table**
- Creates inquiries table with proper schema
- Indexes on: category, status, priority, created_at
- Soft deletes enabled
- Timestamps for created_at, updated_at

**Seeder: InquirySeeder**
- Creates 10 sample inquiries
- Covers all categories and statuses
- Useful for testing and development

**Factory: InquiryFactory**
- Generates fake inquiry data for testing
- State methods for different scenarios
- Supports method chaining

### 8. Tests (`tests/Feature/`)

**InquiryApiTest.php**
- Comprehensive feature tests
- Tests all CRUD operations
- Tests validation
- Tests filtering and search
- Tests error handling
- Uses RefreshDatabase trait

## Design Patterns

### 1. Service Layer Pattern
Business logic is encapsulated in `InquiryService`, keeping controllers thin and focused on HTTP concerns.

### 2. Repository Pattern
Eloquent ORM acts as the repository layer with custom query scopes for reusable queries.

### 3. Request Validation Pattern
Dedicated Form Request classes handle validation, keeping validation logic separate and reusable.

### 4. Resource Transformation Pattern
API Resources transform models to consistent JSON responses, hiding internal implementation details.

### 5. Transaction Pattern
All write operations are wrapped in database transactions to ensure data integrity.

## Data Flow

### Creating an Inquiry

```
HTTP POST Request
    ↓
InquiryController@store
    ↓
StoreInquiryRequest (validation)
    ↓
InquiryService@createInquiry
    ↓
DB::transaction
    ↓
Inquiry::create (Eloquent)
    ↓
Log::info (audit logging)
    ↓
InquiryResource (transformation)
    ↓
JSON Response
```

### Retrieving Inquiries

```
HTTP GET Request
    ↓
InquiryController@index
    ↓
InquiryService@getAllInquiries
    ↓
Query Builder (with filters)
    ↓
Pagination
    ↓
InquiryCollection (transformation)
    ↓
JSON Response
```

## Error Handling

### Levels of Error Handling

1. **Validation Errors (422)**
   - Handled by Form Request classes
   - Returns structured validation errors

2. **Not Found Errors (404)**
   - Handled by ModelNotFoundException
   - Returns user-friendly error message

3. **Server Errors (500)**
   - Caught in controller try-catch blocks
   - Logged with full context
   - Returns safe error message (hides details in production)

### Logging Strategy

All operations are logged with context:
- Inquiry creation: ID, category, email
- Inquiry updates: ID, changed fields, old/new status
- Inquiry deletion: ID, category
- Errors: Full exception with stack trace

Logs location: `storage/logs/laravel.log`

## Security Features

1. **Mass Assignment Protection**: `$fillable` property in model
2. **SQL Injection Prevention**: Eloquent ORM parameterized queries
3. **Input Validation**: Form Request validation on all inputs
4. **Soft Deletes**: Audit trail maintained
5. **Error Message Sanitization**: Debug info only shown in development

## Database Schema

### Inquiries Table

| Column | Type | Nullable | Default | Index |
|--------|------|----------|---------|-------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | PRIMARY |
| name | VARCHAR(255) | NO | - | - |
| email | VARCHAR(255) | NO | - | - |
| phone | VARCHAR(20) | YES | NULL | - |
| category | ENUM | NO | - | YES |
| subject | VARCHAR(255) | NO | - | - |
| message | TEXT | NO | - | - |
| status | ENUM | NO | 'pending' | YES |
| priority | ENUM | NO | 'medium' | YES |
| resolved_at | TIMESTAMP | YES | NULL | - |
| resolution_notes | TEXT | YES | NULL | - |
| created_at | TIMESTAMP | YES | NULL | YES |
| updated_at | TIMESTAMP | YES | NULL | - |
| deleted_at | TIMESTAMP | YES | NULL | - |

### Enums

**Category**: Trading, Market Data, Technical Issues, General Questions
**Status**: pending, in_progress, resolved, closed
**Priority**: low, medium, high, urgent

## API Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error message"
}
```

### Validation Error Response
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field": ["Error message"]
  }
}
```

## Testing Strategy

1. **Feature Tests**: Test complete request/response cycle
2. **Database Tests**: Use RefreshDatabase trait
3. **Factory Usage**: Generate test data with factories
4. **Assertions**: Test status codes, JSON structure, database state

Run tests:
```bash
php artisan test
```

## Performance Considerations

1. **Database Indexes**: Added on frequently queried columns
2. **Pagination**: Limits result sets to prevent memory issues
3. **Query Scopes**: Reusable, optimized query methods
4. **Eager Loading**: Can be added for relationships if needed
5. **Caching**: Can be implemented for statistics endpoint

## Future Enhancements

Potential improvements:
- Add authentication/authorization
- Implement inquiry assignment to support staff
- Add email notifications
- Implement file attachments
- Add inquiry comments/notes system
- Implement real-time updates via WebSockets
- Add rate limiting
- Implement API versioning
- Add comprehensive unit tests

## Maintenance

### Regular Tasks
- Monitor logs for errors
- Review inquiry statistics
- Database backups
- Update dependencies
- Security patches

### Monitoring
- API response times
- Error rates
- Database query performance
- Storage usage

## Compliance & Auditing

The system is designed with compliance in mind:
- All operations are logged
- Soft deletes maintain audit trail
- Timestamps track all changes
- Transaction handling ensures data integrity
- Comprehensive error logging for troubleshooting
