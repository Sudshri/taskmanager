# TaskFlow — Laravel 13 Task Manager

A clean, fully-featured task management application built with **Laravel 13** and **PHP 8.4**.

Tasks can be created, edited, deleted, and **reordered by dragging** in the browser. Priority is automatically updated based on position (position 1 = highest priority). Tasks can optionally belong to a **Project**, and the task list can be filtered by project using a pill-style filter bar.

---

## Features

- ✅ Create, edit, and delete tasks
- ✅ Priority stored as an integer; automatically re-sequenced (1, 2, 3…) after every reorder or deletion
- ✅ Drag-and-drop reordering with instant DOM feedback; changes are persisted on "Save Order"
- ✅ Project support — tasks belong to a project (optional); filter tasks by project
- ✅ Full CRUD for projects
- ✅ Form Request validation on every input
- ✅ Clean, readable Blade templates with a custom design system (no CSS framework dependencies)
- ✅ Zero JavaScript frameworks — vanilla JS drag-and-drop using the native HTML5 Drag and Drop API

---

## Requirements

| Tool | Minimum version |
|------|----------------|
| PHP | >=8.3
| Composer | 2.x |
| MySQL | 8.0 (or MariaDB 10.6) |
| Node / npm | Only needed if you want to run Vite — not required for this app |

---

## Local Development Setup

### 1. Clone the repository

```bash
git clone https://github.com/sudshri/taskmanager.git
cd taskmanager
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Create the environment file

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Configure your database

Open `.env` and update the database block:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskmanager   # must already exist
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database if it doesn't exist yet:

```bash
mysql -u root -p -e "CREATE DATABASE taskmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. (Optional) Seed sample data

Seeds two projects with tasks and a handful of unassigned tasks so the app is immediately usable.

```bash
php artisan db:seed
```

### 8. Start the development server

```bash
php artisan serve
```

Visit **http://localhost:8000** — you're up and running.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TaskController.php        # CRUD + reorder endpoint
│   │   └── ProjectController.php     # CRUD
│   └── Requests/
│       ├── StoreTaskRequest.php
│       ├── UpdateTaskRequest.php
│       ├── ReorderTasksRequest.php
│       ├── StoreProjectRequest.php
│       └── UpdateProjectRequest.php
└── Models/
    ├── Task.php                       # priority, project relationship, resequence helper
    └── Project.php                    # tasks relationship

database/
├── migrations/
│   ├── ..._create_projects_table.php
│   └── ..._create_tasks_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/views/
├── layouts/
│   └── app.blade.php                 # shared nav, flash messages, design tokens
├── tasks/
│   ├── index.blade.php               # drag-and-drop list + project filter
│   ├── create.blade.php
│   └── edit.blade.php
└── projects/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php

routes/
└── web.php                           # resourceful routes + PATCH /tasks/reorder
```

---

## Key Design Decisions

### Priority management
Priority is stored as an `unsignedInteger` column. When a task is created it is appended at the end of the list. When a task is deleted or moved to a different project, `Task::resequencePriorities()` is called to close any gaps (e.g. 1, 2, 4 → 1, 2, 3). This keeps the column meaningful and easy to reason about.

### Reorder endpoint
`PATCH /tasks/reorder` receives a JSON body `{ "ordered_ids": [3, 1, 5] }`, writes each task's new priority in a loop, and returns `200 OK`. The route is registered **before** the resource group so Laravel's router doesn't try to resolve `"reorder"` as a task ID.

### Form Requests
Every mutation goes through a dedicated `FormRequest` subclass so controllers stay thin and validation rules live in a single, testable location.

### No frontend build step
The app uses a single embedded `<style>` block with CSS custom properties (design tokens) in the layout and plain JS in the task list view. There is no Vite/webpack step required to run the application.

---

## Production Deployment

### Shared hosting / VPS (manual)

```bash
# 1. Upload files (exclude vendor/, .env)
# 2. On the server:
composer install --no-dev --optimize-autoloader
cp .env.example .env
# Edit .env with production values
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Point your web server's document root to the **`public/`** directory.

### Nginx example

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/taskmanager/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Apache example

Ensure `mod_rewrite` is enabled. The included `public/.htaccess` (from Laravel's default scaffold) handles routing automatically.

### Laravel Forge / Ploi

Set the root directory to `public/`, add your environment variables via the dashboard, and trigger a deploy. Both platforms auto-run `composer install` and `php artisan migrate`.



## License

MIT
