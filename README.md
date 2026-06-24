# EzTravel - REST API Server (Backend)

EzTravel Backend is a secure REST API engine built on **Laravel 12** that acts as the service controller for the EzTravel platform. It manages user credentials, handles stateless session authentication (Sanctum tokens), stores bookmarks, and proxies external APIs to prevent CORS policy restrictions.

---

## 🛠 Prerequisites

Ensure you have the following components installed globally:
- **PHP** (v8.2 or higher)
- **Composer** (v2.x or higher)
- **SQLite3** PHP extensions (typically `pdo_sqlite` and `sqlite3` enabled in your `php.ini` file)

---

## 🚀 Installation & Setup

1.  **Install dependencies**:
    ```bash
    composer install
    ```

2.  **Configure environment**:
    Copy the sample environment variables:
    ```bash
    copy .env.example .env
    ```

3.  **Generate encryption key**:
    ```bash
    php artisan key:generate
    ```

4.  **Configure Database**:
    By default, EzTravel uses **SQLite**. Create the local database file:
    - On Windows (PowerShell):
      ```powershell
      New-Item -ItemType File -Path "database/database.sqlite" -Force
      ```
    - On Mac/Linux (Terminal):
      ```bash
      touch database/database.sqlite
      ```

5.  **Run Database Migrations**:
    Setup tables and columns:
    ```bash
    php artisan migrate
    ```

6.  **Launch the Server**:
    Start the local development server:
    ```bash
    php artisan serve
    ```
    The server will start running at `http://localhost:8000`.

---

## 🔒 Authentication & Routes

This server integrates **Laravel Sanctum** to manage authentication tokens securely. All data queries targeting favorites or profile edits must provide a Bearer Token in the headers:
`Authorization: Bearer <your_token>`

### Public Routes
- `POST /api/register` - Create user account (returns user object & Bearer token).
- `POST /api/login` - Verify credentials (returns user object & Bearer token).

### Protected Routes (Requires auth:sanctum)
- `GET /api/user` - Retrieve active profile credentials.
- `PUT /api/user` - Update profile settings (name, email, password).
- `POST /api/logout` - Revoke current access token.
- `GET /api/favorites` - Get the user's saved destinations.
- `POST /api/favorites` - Save a new destination to database.
- `DELETE /api/favorites/{id}` - Unsave a destination by its ID.
- `GET /api/country/{name}` - Fetches and proxies country demographics from REST Countries API to bypass CORS.

### Admin Routes (Requires role:admin validation)
- `GET /api/admin/users` - Get all users with their favorites counts.
- `DELETE /api/admin/users/{id}` - Moderation route to delete user accounts.
