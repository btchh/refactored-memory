# 🗄️ WashHour Database Schema - Entity Relationship Diagram

## 📊 Database Overview

The WashHour system uses a relational database with 11 core tables managing users, bookings, services, products, messaging, and audit trails.

---

## 🔗 Entity Relationships

```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│    USERS    │────────▶│ TRANSACTIONS │◀────────│   ADMINS    │
│             │  1:N    │              │  N:1    │             │
└─────────────┘         └──────────────┘         └─────────────┘
      │                        │  │                      │
      │                        │  │                      │
      │ 1:N                    │  │                      │ 1:N
      │                        │  │                      │
      ▼                        │  │                      ▼
┌─────────────┐               │  │              ┌─────────────┐
│CONVERSATIONS│               │  │              │  SERVICES   │
│             │               │  │              │             │
└─────────────┘               │  │              └─────────────┘
      │                        │  │                      │
      │ 1:N                    │  │                      │ N:M
      │                        │  │                      │
      ▼                        │  │                      ▼
┌─────────────┐               │  │              ┌─────────────┐
│  MESSAGES   │               │  │              │   SERVICE   │
│             │               │  │              │TRANSACTIONS │
└─────────────┘               │  │              └─────────────┘
                               │  │                      ▲
                               │  │                      │
                               │  │ N:M                  │
                               │  └──────────────────────┘
                               │
                               │ N:M
                               │
                               ▼
                        ┌─────────────┐
                        │  PRODUCTS   │◀────────┐
                        │             │  1:N    │
                        └─────────────┘         │
                               │                │
                               │ N:M            │
                               │                │
                               ▼                │
                        ┌─────────────┐         │
                        │   PRODUCT   │         │
                        │TRANSACTIONS │         │
                        └─────────────┘         │
                                                │
                                         ┌─────────────┐
                                         │   ADMINS    │
                                         │             │
                                         └─────────────┘
                                                │
                                                │ 1:N
                                                │
                                                ▼
                                         ┌─────────────┐
                                         │ AUDIT_LOGS  │
                                         │             │
                                         └─────────────┘
```

---

## 📋 Table Definitions

### 👤 **USERS** (Customer Accounts)
**Purpose:** Store customer information and authentication

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| username | VARCHAR(255) | UNIQUE, NOT NULL | Login username |
| fname | VARCHAR(255) | NOT NULL | First name |
| lname | VARCHAR(255) | NOT NULL | Last name |
| address | VARCHAR(255) | NOT NULL | Customer address |
| latitude | DECIMAL(10,8) | NULLABLE | Geocoded latitude |
| longitude | DECIMAL(11,8) | NULLABLE | Geocoded longitude |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email address |
| phone | VARCHAR(255) | UNIQUE, NOT NULL | Phone number |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| status | ENUM | DEFAULT 'active' | active, disabled |
| remember_token | VARCHAR(100) | NULLABLE | Remember me token |
| created_at | TIMESTAMP | | Account creation |
| updated_at | TIMESTAMP | | Last update |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete (archived) |

**Relationships:**
- Has many `transactions` (bookings)
- Has many `conversations` (chat threads)

---

### 👨‍💼 **ADMINS** (Branch Managers/Staff)
**Purpose:** Store admin/staff accounts and branch information

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| admin_name | VARCHAR(255) | NOT NULL | Branch/Business name |
| fname | VARCHAR(255) | NOT NULL | Admin first name |
| lname | VARCHAR(255) | NOT NULL | Admin last name |
| address | VARCHAR(255) | NOT NULL | Admin personal address |
| branch_address | VARCHAR(255) | NULLABLE | Branch location |
| branch_latitude | DECIMAL(10,8) | NULLABLE | Branch latitude |
| branch_longitude | DECIMAL(11,8) | NULLABLE | Branch longitude |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email address |
| phone | VARCHAR(255) | UNIQUE, NOT NULL | Phone number |
| latitude | DECIMAL(10,8) | NULLABLE | Admin location latitude |
| longitude | DECIMAL(11,8) | NULLABLE | Admin location longitude |
| location_updated_at | TIMESTAMP | NULLABLE | Last location update |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| remember_token | VARCHAR(100) | NULLABLE | Remember me token |
| created_at | TIMESTAMP | | Account creation |
| updated_at | TIMESTAMP | | Last update |

**Relationships:**
- Has many `transactions` (bookings handled)
- Has many `services` (branch-specific services)
- Has many `products` (branch-specific products)
- Has many `audit_logs` (action history)

---

### 📦 **TRANSACTIONS** (Bookings/Orders)
**Purpose:** Store customer bookings and order details

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT | FK → users.id, NULLABLE, CASCADE | Customer (null for walk-ins) |
| admin_id | BIGINT | FK → admins.id, NULLABLE, SET NULL | Assigned branch/admin |
| booking_date | DATE | NOT NULL | Scheduled date |
| booking_time | TIME | NOT NULL | Scheduled time |
| pickup_address | VARCHAR(255) | NOT NULL | Pickup location |
| latitude | DECIMAL(10,8) | NULLABLE | Pickup latitude |
| longitude | DECIMAL(11,8) | NULLABLE | Pickup longitude |
| item_type | ENUM | NOT NULL | clothes, comforter, shoes |
| pickup_method | ENUM | DEFAULT 'branch_pickup' | branch_pickup, customer_dropoff |
| delivery_method | ENUM | DEFAULT 'branch_delivery' | branch_delivery, customer_pickup |
| notes | TEXT | NULLABLE | Special instructions |
| calapi_event_id | VARCHAR(255) | NULLABLE | Calendar event ID |
| weight | DECIMAL(10,2) | NULLABLE | Laundry weight (kg) |
| total_price | DECIMAL(10,2) | NOT NULL | Total order amount |
| status | ENUM | DEFAULT 'pending' | pending, in_progress, completed, cancelled |
| completed_at | TIMESTAMP | NULLABLE | Completion timestamp |
| booking_type | ENUM | DEFAULT 'online' | online, walkin |
| created_at | TIMESTAMP | | Order creation |
| updated_at | TIMESTAMP | | Last update |

**Indexes:**
- `booking_date` - Fast date queries
- `user_id` - Customer lookup
- `status` - Status filtering

**Relationships:**
- Belongs to `users` (customer) - NULLABLE for walk-in support
  - ON DELETE CASCADE: When user deleted, their transactions are removed
- Belongs to `admins` (branch handler) - NULLABLE for unassigned bookings
  - ON DELETE SET NULL: When admin deleted, transactions remain but admin_id becomes null
- Has many `service_transactions` (many-to-many with services)
- Has many `product_transactions` (many-to-many with products)

---

### 🧼 **SERVICES** (Laundry Services)
**Purpose:** Store available laundry services per branch

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| admin_id | BIGINT | FK → admins.id, NULLABLE | Branch owner |
| service_name | VARCHAR(255) | NOT NULL | Service name |
| price | DECIMAL(10,2) | NOT NULL | Service price |
| item_type | ENUM | NOT NULL | clothes, comforter, shoes |
| description | TEXT | NULLABLE | Service details |
| is_bundle | BOOLEAN | DEFAULT false | Bundle service flag |
| bundle_items | JSON | NULLABLE | Bundle contents |
| created_at | TIMESTAMP | | Creation time |
| updated_at | TIMESTAMP | | Last update |

**Relationships:**
- Belongs to `admins` (branch)
- Has many `service_transactions` (many-to-many with transactions)

---

### 🧴 **PRODUCTS** (Laundry Products)
**Purpose:** Store available products (detergents, softeners, etc.) per branch

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| admin_id | BIGINT | FK → admins.id, NULLABLE | Branch owner |
| product_name | VARCHAR(255) | NOT NULL | Product name |
| price | DECIMAL(10,2) | NOT NULL | Product price |
| item_type | ENUM | NOT NULL | clothes, comforter, shoes |
| description | TEXT | NULLABLE | Product details |
| created_at | TIMESTAMP | | Creation time |
| updated_at | TIMESTAMP | | Last update |

**Relationships:**
- Belongs to `admins` (branch)
- Has many `product_transactions` (many-to-many with transactions)

---

### 🔗 **SERVICE_TRANSACTIONS** (Pivot Table)
**Purpose:** Link transactions with services (many-to-many)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| transaction_id | BIGINT | FK → transactions.id | Booking reference |
| service_id | BIGINT | FK → services.id | Service reference |
| price_at_purchase | DECIMAL(10,2) | NOT NULL | Price snapshot |
| created_at | TIMESTAMP | | Link creation |
| updated_at | TIMESTAMP | | Last update |

**Relationships:**
- Belongs to `transactions`
- Belongs to `services`

---

### 🔗 **PRODUCT_TRANSACTIONS** (Pivot Table)
**Purpose:** Link transactions with products (many-to-many)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| transaction_id | BIGINT | FK → transactions.id | Booking reference |
| product_id | BIGINT | FK → products.id | Product reference |
| price_at_purchase | DECIMAL(10,2) | NOT NULL | Price snapshot |
| created_at | TIMESTAMP | | Link creation |
| updated_at | TIMESTAMP | | Last update |

**Relationships:**
- Belongs to `transactions`
- Belongs to `products`

---

### 💬 **CONVERSATIONS** (Chat Threads)
**Purpose:** Store chat conversations between users and branches

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT | FK → users.id | Customer |
| branch_address | VARCHAR(255) | NOT NULL | Branch identifier |
| last_message_at | TIMESTAMP | NULLABLE | Last activity |
| created_at | TIMESTAMP | | Thread creation |
| updated_at | TIMESTAMP | | Last update |

**Indexes:**
- `UNIQUE(user_id, branch_address)` - One conversation per user-branch pair
- `last_message_at` - Sort by activity

**Relationships:**
- Belongs to `users`
- Has many `messages`

---

### 💬 **MESSAGES** (Chat Messages)
**Purpose:** Store individual chat messages

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| conversation_id | BIGINT | FK → conversations.id | Thread reference |
| sender_type | ENUM | NOT NULL | user, admin |
| sender_id | BIGINT | NOT NULL | User or Admin ID |
| message | TEXT | NOT NULL | Message content |
| is_read | BOOLEAN | DEFAULT false | Read status |
| read_at | TIMESTAMP | NULLABLE | Read timestamp |
| created_at | TIMESTAMP | | Message sent |
| updated_at | TIMESTAMP | | Last update |

**Indexes:**
- `(conversation_id, created_at)` - Thread messages
- `(sender_type, sender_id)` - Sender lookup

**Relationships:**
- Belongs to `conversations`
- Polymorphic relationship to `users` or `admins` (via sender_type/sender_id)

---

### 📝 **AUDIT_LOGS** (Activity Tracking)
**Purpose:** Track all admin actions for accountability

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| admin_id | BIGINT | FK → admins.id | Admin who performed action |
| action | VARCHAR(255) | NOT NULL | Action type (created, updated, etc.) |
| model_type | VARCHAR(255) | NULLABLE | Affected model class |
| model_id | BIGINT | NULLABLE | Affected record ID |
| description | VARCHAR(255) | NOT NULL | Human-readable description |
| old_values | JSON | NULLABLE | Before state |
| new_values | JSON | NULLABLE | After state |
| ip_address | VARCHAR(255) | NULLABLE | Request IP |
| created_at | TIMESTAMP | | Action timestamp |
| updated_at | TIMESTAMP | | Last update |

**Indexes:**
- `(admin_id, created_at)` - Admin activity timeline
- `(model_type, model_id)` - Record history

**Relationships:**
- Belongs to `admins`

---

## 🔄 Relationship Summary

### One-to-Many (1:N)

| Parent | Child | Description |
|--------|-------|-------------|
| **users** | transactions | One customer can have many bookings |
| **admins** | transactions | One branch can handle many bookings |
| **admins** | services | One branch can offer many services |
| **admins** | products | One branch can sell many products |
| **admins** | audit_logs | One admin can have many logged actions |
| **users** | conversations | One user can have many chat threads |
| **conversations** | messages | One thread can have many messages |

### Many-to-Many (N:M)

| Table A | Pivot Table | Table B | Description |
|---------|-------------|---------|-------------|
| **transactions** | service_transactions | **services** | Bookings can include multiple services |
| **transactions** | product_transactions | **products** | Bookings can include multiple products |

### Polymorphic Relationships

| Table | Relationship | Description |
|-------|--------------|-------------|
| **messages** | sender (user/admin) | Messages can be sent by either users or admins |

---

## 🎯 Key Design Decisions

### 1. **Global User Management**
- All branches can view and manage all users system-wide
- User status (active/disabled/archived) is global across all branches
- Each branch can see user's complete booking history across all branches
- Branch-specific statistics are shown alongside global user data
- Enables consistent customer experience across multiple branch locations

### 2. **Soft Deletes on Users**
- Users are archived (soft deleted) instead of permanently removed
- Preserves booking history and data integrity
- `deleted_at` column tracks archival
- Archived users can be restored by any branch admin

### 3. **Nullable user_id in Transactions**
- Supports walk-in customers without accounts
- `booking_type` field distinguishes online vs walk-in
- Maintains data for guest transactions
- Walk-in bookings don't require user registration

### 3.1. **Clear Pickup & Delivery Methods**
- `pickup_method` defines how laundry arrives at branch:
  - `branch_pickup`: Branch picks up from customer location
  - `customer_dropoff`: Customer drops off at branch
- `delivery_method` defines how laundry returns to customer:
  - `branch_delivery`: Branch delivers to customer location
  - `customer_pickup`: Customer picks up from branch
- Four service combinations:
  1. **Full Service**: branch_pickup + branch_delivery (most convenient)
  2. **Self Drop-off**: customer_dropoff + branch_delivery (save on pickup fee)
  3. **Self Pickup**: branch_pickup + customer_pickup (save on delivery fee)
  4. **Self Service**: customer_dropoff + customer_pickup (cheapest option)

### 4. **Price Snapshots in Pivot Tables**
- `price_at_purchase` stores historical pricing
- Protects against price changes affecting past orders
- Accurate financial reporting and audit trails

### 5. **Branch Grouping via branch_address**
- Multiple admins can manage the same branch
- Conversations grouped by branch, not individual admin
- Flexible multi-admin support per location
- Enables branch-level analytics and reporting

### 6. **Polymorphic Messages**
- `sender_type` + `sender_id` allows flexible sender identification
- Single messages table for both user and admin messages
- Efficient querying and storage
- Supports future sender types if needed

### 7. **Comprehensive Indexing**
- Date-based indexes for fast booking queries
- Status indexes for filtering
- Composite indexes for common query patterns
- Optimized for both global and branch-specific queries

---

## 📈 Data Flow Examples

### Booking Creation Flow
```
1. User selects services/products
2. Transaction created (status: pending)
3. Service_transactions records created (with price snapshots)
4. Product_transactions records created (with price snapshots)
5. Total_price calculated and stored
6. CalAPI event created (calapi_event_id stored)
7. SMS notification sent to user
```

### Walk-in Booking Flow
```
1. Admin selects "Walk-in" booking type
2. Transaction created with user_id = NULL
3. booking_type = 'walkin'
4. Service/product selection continues normally
5. No user account required
```

### Message Flow
```
1. User initiates chat with branch
2. Conversation created (user_id + branch_address)
3. Messages created with sender_type='user'
4. Admin responds with sender_type='admin'
5. last_message_at updated on conversation
```

### Audit Trail Flow
```
1. Admin performs action (update booking status)
2. Audit_log created with:
   - admin_id (who)
   - action (what)
   - model_type + model_id (where)
   - old_values + new_values (changes)
   - ip_address (from where)
```

---

## 🔐 Data Integrity Rules

### Foreign Key Constraints

#### CASCADE Deletes
- **users → transactions**: When a user is deleted, all their transactions are automatically deleted
- **users → conversations**: When a user is deleted, all their conversations are removed
- **conversations → messages**: When a conversation is deleted, all messages in that thread are removed
- **transactions → service_transactions**: When a transaction is deleted, all service links are removed
- **transactions → product_transactions**: When a transaction is deleted, all product links are removed

#### SET NULL on Delete
- **admins → transactions**: When an admin is deleted, transaction.admin_id becomes NULL (preserves booking history)
- **admins → services**: When an admin is deleted, service.admin_id becomes NULL (preserves service data)
- **admins → products**: When an admin is deleted, product.admin_id becomes NULL (preserves product data)

### Soft Deletes
- **Users** are soft deleted (archived) using `deleted_at` timestamp
- Bookings and history remain accessible even after user archival
- Archived users can be restored by admins
- Soft deleted users don't appear in normal queries but data is preserved

### Nullable Relationships
- **transactions.user_id**: NULL for walk-in customers without accounts
- **transactions.admin_id**: NULL for unassigned bookings or when admin is deleted
- **services.admin_id**: NULL when owning admin is deleted
- **products.admin_id**: NULL when owning admin is deleted

---

## 📊 Analytics Queries

### Revenue by Period
```sql
SELECT SUM(total_price) 
FROM transactions 
WHERE status = 'completed' 
  AND admin_id IN (branch_admin_ids)
  AND DATE(updated_at) BETWEEN start_date AND end_date
```

### Online vs Walk-in Bookings
```sql
SELECT booking_type, COUNT(*) as count, SUM(total_price) as revenue
FROM transactions
WHERE admin_id IN (branch_admin_ids)
GROUP BY booking_type
```

### Popular Services
```sql
SELECT s.service_name, COUNT(*) as bookings, SUM(st.price_at_purchase) as revenue
FROM services s
JOIN service_transactions st ON s.id = st.service_id
JOIN transactions t ON st.transaction_id = t.id
WHERE t.admin_id IN (branch_admin_ids)
GROUP BY s.id
ORDER BY bookings DESC
```

---

## 🔧 Maintenance Notes

### Regular Tasks
- Clean up old sessions (Laravel handles automatically)
- Archive old audit logs (optional, after 1 year)
- Backup database regularly
- Monitor table sizes and optimize indexes

### Performance Optimization
- Add indexes for frequently queried columns
- Use Redis for caching and sessions
- Enable query caching for analytics
- Consider partitioning transactions table by date (for large datasets)

---

## 📝 Schema Version

**Current Version:** 1.6.0  
**Last Updated:** December 5, 2025  
**Total Tables:** 11 (+ 3 Laravel system tables)

### Recent Changes (v1.6.0)
- Added `completed_at` timestamp field to transactions table
- Tracks exact completion time for completed orders
- Enables accurate completion time analytics and reporting
- Consolidated migration into main create_transactions_table migration

### Previous Changes (v1.5.0)
- Replaced ambiguous `service_type` with clear `pickup_method` and `delivery_method` fields
- Four distinct service combinations now supported
- Better pricing flexibility (charge separately for pickup/delivery)
- Clearer customer communication about service type

### Previous Changes (v1.4.0)
- Implemented global user management across all branches
- All branches can now view and manage all users system-wide
- User status changes affect all branches simultaneously
- Enhanced user views to show both global and branch-specific statistics

### Previous Changes (v1.3.0)
- Consolidated transactions table migrations into single create migration
- Added detailed foreign key constraint documentation (CASCADE vs SET NULL)
- Clarified nullable relationship behaviors
- Enhanced data integrity rules section

---

<p align="center">
  <strong>WashHour Database Schema</strong><br>
  Designed for scalability, performance, and data integrity
</p>
