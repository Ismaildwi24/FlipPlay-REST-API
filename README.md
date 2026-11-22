# FlipPlay REST API — The Vibe Grid (Web Programming Module 4)

A modern REST API powered by **Laravel 12** that advances the Object-Oriented Programming (OOP) foundation from **Module 3** into a production-grade backend architecture featuring MySQL persistence, Eloquent ORM, Many-to-Many Relationships, Form Request Validation, consistent API Resources, Custom Actions, and comprehensive error handling.

---

## 📋 Practicum Requirements & Evaluation Rubric Fulfillment

| Assessment Criteria | Weight | Practicum Requirement | FlipPlay Implementation | Status |
|:---|:---:|:---|:---|:---:|
| **Complete CRUD API** | **20%** | Full `index`, `show`, `store`, `update`, and `destroy` methods with proper HTTP status codes (`200 OK`, `201 Created`, etc.). | `MediaController` implements all standard CRUD methods: `index` (200), `show` (200 / 404), `store` (201), `update` (200), and `destroy` (200). | ✅ **COMPLETED** |
| **Eloquent Relationship** | **20%** | At least 2 entities with Eloquent relationship (`belongsToMany`), proper migration with pivot table, and eager loading (`with()`). | Many-to-Many relationship between `Media` and `User` via the `media_user` pivot table (with extra attributes `status`, `user_rating`, `note`). Eager loading implemented using `with('users')` and `with('media')`. | ✅ **COMPLETED** |
| **Form Request Validation** | **15%** | Server-side validation (POST/PATCH) via Form Requests, localized error messages, and `422 Unprocessable Content` response. | `StoreMediaRequest` (`required` on POST, `sometimes` on PATCH, `unique:media,title`, numeric boundaries) & `AddToCollectionRequest`. | ✅ **COMPLETED** |
| **API Resource** | **10%** | Uniform JSON format using API Resources, leveraging conditional attributes and `whenLoaded()`. | `MediaResource` and `UserResource` serialize consistent output structures and embed relational data only when explicitly loaded (`whenLoaded`). | ✅ **COMPLETED** |
| **Custom Action Beyond CRUD** | **10%** | Dedicated action modifying relationships (attach/detach user collection) with duplicate/race condition handling. | `POST /api/users/{user}/collection` (attaching media to personal collection with duplicate detection `409 Conflict`), `DELETE /api/users/{user}/collection/{media}` (detaching from pivot), and `GET /api/users/{user}/collection`. | ✅ **COMPLETED** |
| **Endpoint Testing** | **5%** | Comprehensive testing covering both happy path and failure path using Postman. | Bundled ready-to-import Postman collection `FlipPlay_API.postman_collection.json` and 20 automated test suites (`php artisan test`). | ✅ **COMPLETED** |

---

## 🗄️ Database Architecture & Eloquent Relationships

```text
┌───────────────────────────────┐                  ┌────────────────────────────────────────┐
│             users             │                  │                 media                  │
├───────────────────────────────┤                  ├────────────────────────────────────────┤
│ id (PK)                       │ 1              * │ id (PK)                                │
│ name (VARCHAR)                │◄───────┐      ┌─►│ title (VARCHAR, UNIQUE)                │
│ username (VARCHAR, UNIQUE)    │        │      │  │ type (VARCHAR: Anime/Movie)            │
│ email (VARCHAR, UNIQUE)       │        │      │  │ genres (JSON: ["Action", "Sci-Fi"])    │
│ favorite_aura (VARCHAR)       │        │      │  │ rating (DECIMAL 3,1)                   │
│ created_at / updated_at       │        │      │  │ release_year (INT)                     │
└───────────────────────────────┘        │      │  │ aura (VARCHAR: Binge-Mode, etc.)       │
                                         │      │  │ synopsis (TEXT)                        │
                                         │      │  │ episode_count, studio, status (Anime)  │
                                         │      │  │ duration_minutes, director, quality    │
                                         │      │  │ created_at / updated_at                │
                                         │      │  └────────────────────────────────────────┘
                                 ┌───────┴──────┴─────────┐
                                 │       media_user       │ (Pivot Table)
                                 ├────────────────────────┤
                                 │ id (PK)                │
                                 │ user_id (FK -> users)  │
                                 │ media_id (FK -> media) │
                                 │ status (VARCHAR)       │
                                 │ user_rating (DECIMAL)  │
                                 │ note (VARCHAR)         │
                                 │ created_at, updated_at │
                                 │ UNIQUE(user_id, media) │
                                 └────────────────────────┘
```

---

## 🌐 REST API Endpoints Reference

All routes are declared in [routes/api.php](routes/api.php):

### 1. Media Catalog (`/api/media`)

| Method | Endpoint | Description | Status Code |
|:---|:---|:---|:---:|
| `GET` | `/api/media` | Retrieve all media items (Pagination 10 items & Eager Loading `users`) | `200 OK` |
| `GET` | `/api/media?title={keyword}` | Search media by title or synopsis (Query Scope `search`) | `200 OK` |
| `GET` | `/api/media?aura={vibe}` | Filter media by aura/vibe (Query Scope `byAura`) | `200 OK` |
| `GET` | `/api/media?type={Anime\|Movie}` | Filter media by type (Query Scope `byType`) | `200 OK` |
| `GET` | `/api/media/{id}` | Retrieve single media detail with collectors | `200 OK` / `404 Not Found` |
| `POST` | `/api/media` | Create new media (Validated by `StoreMediaRequest`) | `201 Created` / `422 Unprocessable` |
| `PATCH` | `/api/media/{id}` | Partially update media attributes (`sometimes` rules) | `200 OK` / `422 Unprocessable` |
| `DELETE` | `/api/media/{id}` | Remove media from catalog | `200 OK` / `404 Not Found` |

### 2. Users & Collections (`/api/users`)

| Method | Endpoint | Description | Status Code |
|:---|:---|:---|:---:|
| `GET` | `/api/users` | List all users along with their saved collections | `200 OK` |
| `GET` | `/api/users/{id}` | Retrieve user profile with complete media collection | `200 OK` / `404 Not Found` |
| `POST` | `/api/users` | Register a new user | `201 Created` / `422 Unprocessable` |
| `GET` | `/api/users/{id}/collection` | **[Custom Action]** Retrieve dedicated collection of a specific user | `200 OK` |
| `POST` | `/api/users/{id}/collection` | **[Custom Action]** Add media to user collection (`attach`) | `201 Created` / `409 Conflict` / `422` |
| `DELETE` | `/api/users/{id}/collection/{media_id}` | **[Custom Action]** Remove media from user collection (`detach`) | `200 OK` / `404 Not Found` |

---

## 🚀 Setup & Execution Guide

### 1. Prerequisites
- **PHP** 8.2 or higher
- **Composer** 2.x
- **MySQL** running (via Laragon or XAMPP) on port 3306

### 2. Environment & Database Configuration
Ensure MySQL service is active, create database `flipplay_db` or adjust configuration in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flipplay_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations & Database Seeding
Execute database migrations and populate seed data:
```bash
php artisan migrate:fresh --seed
```
> This command creates the `users`, `media`, and `media_user` tables and seeds 7 default media items from Module 3 (Attack on Titan, Cyberpunk: Edgerunners, Your Name, Steins;Gate, Interstellar, Spider-Man: Across the Spider-Verse, La La Land) alongside 3 user profiles (`kenjiro`, `aoi_vibes`, `ren_runner`) with initial collections.

### 4. Start Development Server
```bash
php artisan serve
```
The REST API will be accessible at: `http://127.0.0.1:8000`

---

## 🧪 API Testing (Postman & Automated Tests)

### A. Testing via Postman
A pre-configured Postman collection is included in the project root:
📁 [FlipPlay_API.postman_collection.json](FlipPlay_API.postman_collection.json)

1. Open **Postman**.
2. Press **`Ctrl + O`** (or drag & drop the JSON file into Postman).
3. Select `FlipPlay_API.postman_collection.json`.
4. All requests are grouped logically:
   - **1. Media (Katalog)**: Full CRUD operations, Query Filters, Happy Paths (`200`, `201`), and Failure Paths (`404`, `422`).
   - **2. Users & Collections**: Many-to-Many relations, Custom Actions (`addToCollection`, `removeFromCollection`), Happy Paths, and Failure Paths (`409 Conflict` on duplicates, `404 Not Found`, `422 Unprocessable`).

### B. Automated Test Suite (PHPUnit Feature Tests)
The project includes 20 comprehensive unit & feature tests verifying 100% of demo requirements:
```bash
php artisan test
```
Execution results:
```text
PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Feature\MediaApiTest (10 tests)
PASS  Tests\Feature\UserCollectionApiTest (8 tests)

Tests:    20 passed (143 assertions)
Duration: ~0.8s
```

---

## 📦 Project Directory Structure

```text
TWeb_MD4/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── MediaController.php     <- Media CRUD + Eager Loading + Query Filters
│   │   │   └── UserController.php      <- User Profiles + Collection Custom Actions
│   │   ├── Requests/
│   │   │   ├── StoreMediaRequest.php   <- Form Request Validation (POST & PATCH)
│   │   │   └── AddToCollectionRequest.php <- Collection validation rules
│   │   └── Resources/
│   │       ├── MediaResource.php       <- Media JSON format serializer (whenLoaded)
│   │       └── UserResource.php        <- User JSON format serializer (whenLoaded)
│   ├── Models/
│   │   ├── Media.php                   <- Media Eloquent Model (belongsToMany User)
│   │   └── User.php                    <- User Eloquent Model (belongsToMany Media)
│   └── Providers/
├── database/
│   ├── migrations/
│   │   ├── 2026_01_01_000000_create_users_table.php
│   │   ├── 2026_01_01_000001_create_media_table.php
│   │   └── 2026_01_01_000002_create_media_user_table.php <- Pivot Table
│   └── seeders/
│       └── DatabaseSeeder.php          <- Initial catalog and collection seed data
├── routes/
│   └── api.php                         <- REST API routes & custom action endpoints
├── tests/
│   └── Feature/
│       ├── MediaApiTest.php            <- Media CRUD & validation test suites
│       └── UserCollectionApiTest.php   <- Relational & custom action test suites
├── FlipPlay_API.postman_collection.json <- Ready-to-import Postman collection
├── modul3_legacy/                      <- Archived CLI simulation code from Module 3
└── README.md
```
