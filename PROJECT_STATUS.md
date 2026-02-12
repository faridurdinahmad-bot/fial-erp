# Project Status

## 1. Current Stack

- **Language & Framework**
  - PHP 8.4
  - Laravel 12
- **Infrastructure**
  - MySQL 8
  - Redis 7
- **Data & Tenancy**
  - UUID primary keys across core tables
  - Multi-tenant design (`company_id` on tenant-aware tables)
  - `BaseTenantModel` with `CompanyScope` enforcing tenant isolation at the ORM layer

## 2. Completed Modules

- **Auth**
  - Laravel Breeze (Blade) for authentication scaffolding
  - Multi-tenant user association via `company_id`

- **Brands**
  - Logo images stored as WebP
  - Image processing: resize to 500×500
  - `brand_code` auto-generated per company (e.g. `BR-0001`)
  - Logo storage under `storage/app/public/brands`

- **Units**
  - Professional structure with `short_name`, `type`, and `decimal_allowed`
  - Per-company uniqueness on `short_name`
  - Demo seeder for common units (KG, GM, PCS, etc.)

- **Warranties**
  - Basic CRUD wired into dashboard layout
  - Tenant-aware via `BaseTenantModel`

- **Categories (production-ready)**
  - **Auto code**: `CAT-XXXX` sequence per company
  - **Auto slug**: generated from `name`
  - **Parent/Subcategory system**:
    - `parent_id` for hierarchical categories
    - Support for main and subcategories
  - **Searchable dropdown**:
    - TomSelect-based parent category selector
    - Non-AJAX in create form (preloaded tenant categories)
  - **AJAX filters on index**:
    - Search by name/code
    - Type filter (Main/Sub)
    - Parent filter
    - Partial updates for table + pagination
  - **Image handling**:
    - WebP only
    - Main image: 800×800
    - Thumbnail: 150×150
    - Stored under `storage/app/public/categories`
  - **Clean demo seeder**:
    - For each company:
      - 3 main categories: `Category 0`, `Category 1`, `Category 2`
      - Each main has 3 subcategories:
        - `Category 0 - Sub 0..2`
        - `Category 1 - Sub 0..2`
        - `Category 2 - Sub 0..2`
    - `code`, `slug`, `company_id`, and UUID handled automatically by the model

## 3. Architectural Rules

- **Images**
  - WebP-only image storage for consistency and efficiency
  - Centralized image processing via Intervention Image
- **Tenancy**
  - Tenant isolation is mandatory in all tenant-aware queries
  - All tenant models must extend `BaseTenantModel` (and thus use `CompanyScope`)
- **Structure**
  - Clean, modular controllers and Blade views
  - Consistent Tailwind-based UI in `layouts.dashboard`
  - Clear separation of concerns (models, controllers, views, seeders)
- **Longevity**
  - Designed for production-grade use and horizontal growth
  - Targeted to be maintainable and scalable for 10+ years

## 4. Current State

- **Category module**:
  - Complete, tested, and considered stable
  - Supports hierarchical categories, codes, slugs, filters, and imaging
- **Overall project**:
  - Core foundation and catalog/master data modules (Brands, Units, Warranties, Categories) are in place
  - **Ready to move to next module**: Products refinement and packaging/stock integration

