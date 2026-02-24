# highbase – Laravel E-Commerce Platform

A Laravel-based e-commerce platform with a dynamic, hierarchical product category system and flexible attribute management.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Database Structure](#database-structure)
4. [System Architecture](#system-architecture)
5. [Feature Tests](#feature-tests)
6. [routs](#routes)

---

## Requirements

- PHP >= 8.2
- Composer
- MySQL / SQLite (for testing)
- Laravel 11.x

---

## Installation

```bash
# 1. Clone / navigate to the project
cd your project path 

# 2. Install dependencies
composer install

# 3. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=highbase
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. (Optional) Seed demo data
php artisan db:seed

# 7. Start the server
php artisan serve
```

---

## Database Structure

### Entity-Relationship Overview

```
categories (self-referencing hierarchy)
    │
    ├── category_attributes  (attributes per category, with is_required flag)
    │
    └── products
            │
            └── product_attribute_values  (actual attribute values per product)
```

---

### Table: `categories`

Stores the product categories with support for unlimited nesting via a **self-referencing foreign key**.

| Column        | Type         | Notes                                    |
|---------------|--------------|------------------------------------------|
| `id`          | bigint PK    |                                          |
| `name`        | varchar(255) | Category display name                    |
| `slug`        | varchar(255) | URL-friendly unique identifier           |
| `description` | text         | Optional                                 |
| `parent_id`   | bigint FK    | References `categories.id`; NULL = root  |
| `created_at`  | timestamp    |                                          |
| `updated_at`  | timestamp    |                                          |

**Example rows:**

| id | name       | parent_id | Breadcrumb              |
|----|------------|-----------|-------------------------|
| 1  | Food       | NULL      | Food                    |
| 2  | Vegetables | 1         | Food > Vegetables       |
| 3  | Leaf       | 2         | Food > Vegetables > Leaf|
| 4  | Clothes    | NULL      | Clothes                 |
| 5  | Shirts     | 4         | Clothes > Shirts        |

---

### Table: `category_attributes`

Defines the **schema** of attributes that products in a given category must or may have.

| Column        | Type                             | Notes                                            |
|---------------|----------------------------------|--------------------------------------------------|
| `id`          | bigint PK                        |                                                  |
| `category_id` | bigint FK → `categories.id`      | Cascade delete                                   |
| `name`        | varchar(255)                     | e.g. "Size", "Color", "Expiry Date"              |
| `type`        | enum(text, number, select, boolean) | Controls the input type in the product form   |
| `is_required` | boolean                          | If true, products must supply this attribute     |
| `options`     | json / NULL                      | Allowed values for `select` type                 |
| `created_at`  | timestamp                        |                                                  |
| `updated_at`  | timestamp                        |                                                  |

**Unique constraint:** `(category_id, name)` — no duplicate attribute names per category.

**Example rows:**

| id | category_id | name         | type    | is_required | options                       |
|----|-------------|--------------|---------|-------------|-------------------------------|
| 1  | 1 (Food)    | Expiry Date  | text    | true        | NULL                          |
| 2  | 4 (Clothes) | Size         | select  | true        | ["XS","S","M","L","XL","XXL"] |
| 3  | 4 (Clothes) | Color        | text    | true        | NULL                          |

---

### Table: `products`

Stores the core product data.

| Column        | Type                              | Notes                              |
|---------------|-----------------------------------|------------------------------------|
| `id`          | bigint PK                         |                                    |
| `category_id` | bigint FK → `categories.id`       | Restrict delete (can't delete a category that has products) |
| `name`        | varchar(255)                      |                                    |
| `slug`        | varchar(255) UNIQUE               | Auto-generated from name           |
| `description` | text                              | Optional                           |
| `price`       | decimal(10,2)                     |                                    |
| `stock`       | unsigned int                      |                                    |
| `status`      | enum(active, inactive, draft)     | Default: active                    |
| `created_at`  | timestamp                         |                                    |
| `updated_at`  | timestamp                         |                                    |

---

### Table: `product_attribute_values`

Stores the **actual values** for each attribute of a product (Entity-Attribute-Value pattern).

| Column                  | Type                              | Notes                            |
|-------------------------|-----------------------------------|----------------------------------|
| `id`                    | bigint PK                         |                                  |
| `product_id`            | bigint FK → `products.id`         | Cascade delete                   |
| `category_attribute_id` | bigint FK → `category_attributes.id` | Cascade delete                |
| `value`                 | text                              | Stored as text, typed by attribute |
| `created_at`            | timestamp                         |                                  |
| `updated_at`            | timestamp                         |                                  |

**Unique constraint:** `(product_id, category_attribute_id)` — one value per attribute per product.

**Example rows:**

| id | product_id | category_attribute_id | value      |
|----|------------|-----------------------|------------|
| 1  | 1 (Spinach)| 1 (Expiry Date)       | 2025-03-15 |
| 2  | 2 (T-Shirt)| 2 (Size)              | M          |
| 3  | 2 (T-Shirt)| 3 (Color)             | Navy Blue  |

---

## System Architecture

### 1. Hierarchical Categories (Self-Referencing Tree)

The `categories` table implements an **Adjacency List** model. Each category optionally points to a `parent_id`. This gives:

- Unlimited depth: `Food → Vegetables → Leaf → ...`
- Simple SQL queries for direct parent/children
- The `Category` model provides:
  - `parent()` — BelongsTo the parent
  - `children()` — HasMany direct sub-categories
  - `allChildren()` — Recursive eager-load of all descendants
  - `breadcrumb` — Computed attribute returning `"Food > Vegetables > Leaf"`

### 2. Dynamic Category Attributes (EAV Pattern)

Rather than a fixed schema for every product type, we use an **Entity-Attribute-Value (EAV)** approach:

- `category_attributes` stores the **attribute definitions** per category (the schema)
- `product_attribute_values` stores the **attribute values** per product (the data)

This allows administrators to:
- Add new attribute types to any category at any time
- Mark attributes as required or optional
- Specify options for dropdown (select) attributes
- Use different field types: `text`, `number`, `select`, `boolean`

### 3. Inherited Attributes

Attributes are **inherited** from ancestor categories. For example:
- `Food` has a required attribute `Expiry Date`
- `Vegetables` is a sub-category of `Food`
- A product in `Vegetables` **must also** provide `Expiry Date`

This is implemented in `Category::getAllRequiredAttributes()`, which walks up the parent chain collecting required attributes recursively.

### 4. Dynamic Product Form

When a user selects a category on the product creation/edit form:
1. JavaScript makes a `fetch()` call to `/api/categories/{id}/attributes`
2. The API returns all attributes (own + inherited) as JSON
3. The form dynamically renders the appropriate input fields
4. Required attributes are marked and enforced both client-side (`required` HTML attribute) and server-side (`StoreProductRequest::withValidator`)
5. when update product with new category , its remove the old attributes of old category

### 5. Server-Side Validation

`StoreProductRequest` (and `UpdateProductRequest`) perform two passes of validation:

1. **Base rules** — validates `name`, `price`, `stock`, `status`, `category_id`
2. **After hook** — calls `getAllRequiredAttributes()` on the selected category and verifies every required attribute has a non-empty submitted value. Missing required attributes produce named errors like `attributes.{id}`.

---


## Feature Tests

Run all tests with:

```bash
php artisan test
# or
php artisan test --filter ProductTest
```

### Test cases in `tests/Feature/ProductTest.php`

| Test | Description |
|---|---|
| `test_can_create_product_successfully_without_attributes` | Happy path: product with no required attributes |
| `test_can_create_product_with_required_attributes` | Happy path: all required attributes provided |
| `test_cannot_create_product_with_validation_errors` | Fails: empty name, negative price, bad status |
| `test_cannot_create_product_without_category` | Fails: no category_id |
| `test_cannot_create_product_with_nonexistent_category` | Fails: category doesn't exist |
| `test_cannot_create_product_when_required_attributes_are_missing` | Fails: required attributes absent |
| `test_cannot_create_product_when_only_some_required_attributes_are_provided` | Fails: one required attribute missing |
| `test_required_attributes_are_inherited_from_parent_category` | Fails: parent's required attribute not provided |
| `test_can_create_product_in_child_category_when_all_inherited_attributes_provided` | Happy path: inherited required attribute provided |
| `test_optional_attributes_do_not_block_product_creation` | Optional attributes don't block creation |

## routes

- [Shop](https://highbase.abanoubsamir.com)
- [Admin Dashboard](https://highbase.abanoubsamir.com/admin/products)