# WashHour Database Schema (ERD Reference)

## Tables Overview

```
┌─────────────────┐              ┌─────────────────┐              ┌─────────────────┐
│     users       │              │     admins      │              │   audit_logs    │
├─────────────────┤              ├─────────────────┤              ├─────────────────┤
│ PK id           │              │ PK id           │◄─────────────│ FK admin_id     │
│    username     │              │    admin_name   │     (1:N)    │    action       │
│    fname        │              │    fname        │              │    model_type   │
│    lname        │              │    lname        │              │    model_id     │
│    address      │              │    address      │              │    description  │
│    latitude     │              │    branch_addr  │              │    old_values   │
│    longitude    │              │    branch_lat   │              │    new_values   │
│    email        │              │    branch_lng   │              │    ip_address   │
│    phone        │              │    email        │              │    timestamps   │
│    password     │              │    phone        │              └─────────────────┘
│    status       │              │    latitude     │
│    timestamps   │              │    longitude    │
└────────┬────────┘              │    password     │
         │                       │    timestamps   │
         │                       └────────┬────────┘
         │                                │
         │         ┌──────────────────────┼──────────────────────┐
         │         │                      │                      │
         │         │ (1:N)                │ (1:N)                │ (1:N)
         │         ▼                      ▼                      ▼
         │  ┌─────────────┐        ┌─────────────┐        ┌─────────────────┐
         │  │  services   │        │  products   │        │  conversations  │
         │  ├─────────────┤        ├─────────────┤        ├─────────────────┤
         │  │ PK id       │        │ PK id       │        │ PK id           │
         │  │ FK admin_id │        │ FK admin_id │        │ FK user_id      │◄───┐
         │  │ service_name│        │ product_name│        │ FK admin_id     │    │
         │  │ price       │        │ price       │ (1:N)  │ last_message_at │    │
         │  │ item_type   │        │ item_type   │        │ timestamps      │    │
         │  │ description │        │ description │        └────────┬────────┘    │
         │  │ is_bundle   │        │ timestamps  │                 │             │
         │  │ bundle_items│        └──────┬──────┘                 │ (1:N)       │
         │  │ timestamps  │               │                        ▼             │
         │  └──────┬──────┘               │                ┌─────────────────┐   │
         │         │                      │                │    messages     │   │
         │         │                      │                ├─────────────────┤   │
         │         │                      │                │ PK id           │   │
         │         │                      │                │ FK conversation │   │
         │         │                      │                │    sender_type  │   │
         │         │                      │                │    sender_id    │   │
         │         │                      │                │    message      │   │
         │         │                      │                │    is_read      │   │
         │         │                      │                │    read_at      │   │
         │         │                      │                │    timestamps   │   │
         │         │                      │                └─────────────────┘   │
         │ (1:N)   │ (N:M)                │ (N:M)                                │ (1:N)
         ▼         │                      │                                      │
┌─────────────────┐│                      │                                      │
│  transactions   │◄──────────────────────┴──────────────────────────────────────┘
├─────────────────┤
│ PK id           │
│ FK user_id      │
│ FK admin_id     │
│    booking_date │
│    booking_time │
│    pickup_addr  │
│    latitude     │
│    longitude    │
│    item_type    │
│    service_type │
│    notes        │
│    calapi_id    │
│    weight       │
│    total_price  │
│    status       │
│    timestamps   │
└────────┬────────┘
         │
    ┌────┴────┐
    │  (N:M)  │
    ▼         ▼
┌─────────────────────┐        ┌─────────────────────┐
│ service_transactions│        │ product_transactions│
│    (Pivot Table)    │        │    (Pivot Table)    │
├─────────────────────┤        ├─────────────────────┤
│ PK id               │        │ PK id               │
│ FK transaction_id   │        │ FK transaction_id   │
│ FK service_id       │        │ FK product_id       │
│    price_at_purchase│        │    price_at_purchase│
│    timestamps       │        │    timestamps       │
└─────────────────────┘        └─────────────────────┘
```

## Relationship Legend
- **(1:N)** = One-to-Many
- **(N:M)** = Many-to-Many (via pivot table)

---

## Table Definitions

### 1. users
Customer accounts for the laundry service.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| username | VARCHAR(255) | UNIQUE, NOT NULL | Login username |
| fname | VARCHAR(255) | NOT NULL | First name |
| lname | VARCHAR(255) | NOT NULL | Last name |
| address | VARCHAR(255) | NOT NULL | Home address |
| latitude | DECIMAL(10,8) | NULLABLE | GPS latitude |
| longitude | DECIMAL(11,8) | NULLABLE | GPS longitude |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email address |
| phone | VARCHAR(255) | UNIQUE, NOT NULL | Phone number |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| status | ENUM | DEFAULT 'active' | 'active' or 'disabled' |
| remember_token | VARCHAR(100) | NULLABLE | Session token |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

### 2. admins
Branch administrators/managers.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| admin_name | VARCHAR(255) | NOT NULL | Branch/shop name |
| fname | VARCHAR(255) | NOT NULL | First name |
| lname | VARCHAR(255) | NOT NULL | Last name |
| address | VARCHAR(255) | NOT NULL | Personal address |
| branch_address | VARCHAR(255) | NULLABLE | Branch location |
| branch_latitude | DECIMAL(10,8) | NULLABLE | Branch GPS lat |
| branch_longitude | DECIMAL(11,8) | NULLABLE | Branch GPS lng |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email address |
| phone | VARCHAR(255) | UNIQUE, NOT NULL | Phone number |
| latitude | DECIMAL(10,8) | NULLABLE | Current GPS lat |
| longitude | DECIMAL(11,8) | NULLABLE | Current GPS lng |
| location_updated_at | TIMESTAMP | NULLABLE | Last location update |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| remember_token | VARCHAR(100) | NULLABLE | Session token |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

### 3. services
Laundry services offered (branch-specific pricing).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| admin_id | BIGINT | FK → admins, NULLABLE, CASCADE | Branch owner |
| service_name | VARCHAR(255) | NOT NULL | Service name |
| price | DECIMAL(10,2) | NOT NULL | Service price |
| item_type | ENUM | NOT NULL | 'clothes', 'comforter', 'shoes' |
| description | TEXT | NULLABLE | Service description |
| is_bundle | BOOLEAN | DEFAULT false | Is bundle package |
| bundle_items | JSON | NULLABLE | Items in bundle |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

### 4. products
Add-on products (branch-specific pricing).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| admin_id | BIGINT | FK → admins, NULLABLE, CASCADE | Branch owner |
| product_name | VARCHAR(255) | NOT NULL | Product name |
| price | DECIMAL(10,2) | NOT NULL | Product price |
| item_type | ENUM | NOT NULL | 'clothes', 'comforter', 'shoes' |
| description | TEXT | NULLABLE | Product description |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

### 5. transactions
Booking/order records.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| user_id | BIGINT | FK → users, CASCADE | Customer |
| admin_id | BIGINT | FK → admins, SET NULL | Assigned branch |
| booking_date | DATE | NOT NULL | Scheduled date |
| booking_time | TIME | NOT NULL | Scheduled time |
| pickup_address | VARCHAR(255) | NOT NULL | Pickup location |
| latitude | DECIMAL(10,8) | NULLABLE | Pickup GPS lat |
| longitude | DECIMAL(11,8) | NULLABLE | Pickup GPS lng |
| item_type | ENUM | NOT NULL | 'clothes', 'comforter', 'shoes' |
| service_type | ENUM | DEFAULT 'pickup' | 'pickup' or 'dropoff' |
| notes | TEXT | NULLABLE | Special instructions |
| calapi_event_id | VARCHAR(255) | NULLABLE | Calendar API ID |
| weight | DECIMAL(10,2) | NULLABLE | Laundry weight (kg) |
| total_price | DECIMAL(10,2) | NOT NULL | Total amount |
| status | ENUM | DEFAULT 'pending' | 'pending', 'in_progress', 'completed', 'cancelled' |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

**Indexes:** booking_date, user_id, status

---

### 6. service_transactions (Pivot)
Links transactions to services.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| transaction_id | BIGINT | FK → transactions, CASCADE | Transaction |
| service_id | BIGINT | FK → services, CASCADE | Service |
| price_at_purchase | DECIMAL(10,2) | NOT NULL | Price when booked |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

### 7. product_transactions (Pivot)
Links transactions to products.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| transaction_id | BIGINT | FK → transactions, CASCADE | Transaction |
| product_id | BIGINT | FK → products, CASCADE | Product |
| price_at_purchase | DECIMAL(10,2) | NOT NULL | Price when booked |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

### 8. conversations
Chat threads between users and admins.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| user_id | BIGINT | FK → users, CASCADE | Customer |
| admin_id | BIGINT | FK → admins, CASCADE | Branch admin |
| last_message_at | TIMESTAMP | NULLABLE | Last activity |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

**Unique:** (user_id, admin_id)

---

### 9. messages
Individual chat messages.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| conversation_id | BIGINT | FK → conversations, CASCADE | Thread |
| sender_type | ENUM | NOT NULL | 'user' or 'admin' |
| sender_id | BIGINT | NOT NULL | Sender's ID |
| message | TEXT | NOT NULL | Message content |
| is_read | BOOLEAN | DEFAULT false | Read status |
| read_at | TIMESTAMP | NULLABLE | When read |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

### 10. audit_logs
Activity tracking for admins.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| admin_id | BIGINT | FK → admins, CASCADE | Who performed |
| action | VARCHAR(255) | NOT NULL | Action type |
| model_type | VARCHAR(255) | NOT NULL | Affected model |
| model_id | BIGINT | NULLABLE | Affected record ID |
| description | VARCHAR(255) | NOT NULL | Human-readable |
| old_values | JSON | NULLABLE | Before change |
| new_values | JSON | NULLABLE | After change |
| ip_address | VARCHAR(255) | NULLABLE | Client IP |
| created_at | TIMESTAMP | NULLABLE | Creation timestamp |
| updated_at | TIMESTAMP | NULLABLE | Update timestamp |

---

## Relationships Summary

| Relationship | Type | Description |
|--------------|------|-------------|
| users → transactions | 1:N | User has many bookings |
| admins → transactions | 1:N | Admin manages many bookings |
| admins → services | 1:N | Admin owns branch services |
| admins → products | 1:N | Admin owns branch products |
| admins → audit_logs | 1:N | Admin has activity history |
| transactions ↔ services | N:M | Via service_transactions |
| transactions ↔ products | N:M | Via product_transactions |
| users ↔ admins | N:M | Via conversations |
| conversations → messages | 1:N | Thread has many messages |
