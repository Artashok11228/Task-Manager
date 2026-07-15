# MVP Roadmap — Task Manager

> This roadmap is based on the **actual state** of this repository, not on a generic task-manager plan.

---

## Current State — Summary

- Laravel 12 with Breeze for web authentication, Sanctum for API authentication, and Spatie Permission
- A partially implemented Task API with an inconsistent schema
- No product UI for task management
- Estimated MVP completion: **approximately 22%**

### Estimation Method

A total of 23 MVP areas were evaluated. Each area received a completion score: Done = 100, Mostly done = 75, In progress = 50, Started = 25, Not started = 0. The average is **approximately 22%**.

This is an **engineering estimate**, not a precise product metric.

---

## Milestone 0 — Stabilize the Foundation — Blocking Fixes

**Goal:** Resolve the schema and controller inconsistencies that block reliable implementation of later features.

**Why now:** Assignment, status workflows, and authorization cannot be implemented safely until the Task schema is corrected.

**Existing files:**
- `database/migrations/2026_05_29_085546_create_tasks_table.php`
- `database/migrations/2026_05_29_091832_create_statuses_table.php`
- `app/Models/Task.php`
- `app/Models/Status.php`
- `app/Http/Controllers/TasksController.php`
- `database/seeders/DatabaseSeeder.php`

**Files to create or modify:**
- New migration: `add_user_id_and_status_id_to_tasks_table`
- `Task.php`
- `Status.php`
- `TasksController.php`
- `TaskFactory.php`
- `DatabaseSeeder.php`

**Tasks:**
1. Add `user_id` to `tasks` as a foreign key to `users`
2. Replace or migrate `tasks.status` from an integer field to `status_id`, referencing `statuses`
3. Update `$fillable`, model relationships, and request validation
4. Set `user_id` from `auth()->id()` in `addTask` and `editTask` under Sanctum authentication
5. Change `getAll` to use `auth()->user()->tasks()` or an equivalent ownership scope
6. Correct the `getAll` response structure by removing the unnecessary nested array
7. Remove the unused `TaskListExtension` import
8. Add `color` to `Status::$fillable`
9. Seed several sample tasks

**Dependencies:** None. This should be the first milestone.

**Acceptance criteria:**
- [ ] `php artisan migrate:fresh --seed` completes without errors
- [ ] `Task::with(['user', 'status'])` successfully loads both relationships
- [ ] The create-task API correctly sets `user_id` and `status_id`
- [ ] `GET /api/tasks` returns only the authenticated user's tasks
- [ ] API responses use a consistent JSON structure

**Suggested tests:**
- `tests/Feature/TaskApiTest.php` covering CRUD operations and ownership isolation

**Complexity:** Medium

**Risks:**
- Migrating existing data if production data already exists
- Product decision required: whether the separate `completed` boolean should remain once status is normalized

---

## Milestone 1 — Complete `RolesController` and Basic Authorization

**Goal:** Make Spatie Permission operational and enforce basic access control.

**Why after Milestone 0:** Once tasks are scoped to users, permissions can be implemented against a reliable ownership model.

**Existing files:**
- `app/Http/Controllers/RolesController.php`
- `config/permission.php`
- `app/Models/User.php`, which uses `HasRoles`

**Tasks:**
1. Complete `RolesController@addRole` using `Role::create(['name' => ..., 'guard_name' => 'web'])`
2. Seed the `admin` and `member` roles
3. Assign a role to the seeded user
4. Apply `permission:` middleware or a Task Policy to Task endpoints
5. Decide which guard to use: `web` or `sanctum`

**Acceptance criteria:**
- [ ] `POST /api/roles` creates a role and returns a JSON response
- [ ] A user without permission cannot view or modify another user's tasks

**Tests:** Role creation and unauthorized access returning HTTP 403

**Complexity:** Medium

**Risks:** Guard mismatch between Breeze web authentication and Sanctum API authentication

---

## Milestone 2 — Task Management Web UI

**Goal:** Deliver the first usable product experience by allowing users to manage tasks in the browser.

**Why now:** Once the API and schema are stable, the product's largest remaining gap is the user interface.

**Existing files:**
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/navigation.blade.php`
- Breeze components under `resources/views/components/`

**New files:**
- `app/Http/Controllers/TaskWebController.php`, or an intentional extension of `TasksController`
- `resources/views/tasks/index.blade.php`
- `resources/views/tasks/create.blade.php`
- `resources/views/tasks/edit.blade.php`
- Resource routes in `routes/web.php`
- `StoreTaskRequest`
- `UpdateTaskRequest`

**Tasks:**
1. Use session-authenticated web routes instead of Sanctum for Blade pages — **recommended**
2. Build CRUD Blade pages using Tailwind CSS and Alpine.js
3. Add a Tasks link to the main navigation
4. Add simple task statistics to the dashboard, grouped by status
5. Populate the status dropdown from `Status::all()`

**Acceptance criteria:**
- [ ] An authenticated user can create, view, edit, and delete tasks
- [ ] Users can see only their own tasks
- [ ] The dashboard displays basic task statistics

**Tests:** `TaskWebTest.php` with browser-style feature tests

**Complexity:** Large

**Risks:** Maintaining two separate access paths, API and Web, without a clear authentication strategy

---

## Milestone 3 — Unify or Formalize Authentication

**Goal:** Remove authentication ambiguity or intentionally document and support a dual-mode architecture.

**Options:**
- **A — Web only:** Use session authentication and remove or restrict the API
- **B — SPA-ready:** Use stateful Sanctum authentication with a dedicated frontend
- **C — Dual mode:** Support both web sessions and API tokens with explicit documentation and tests

**Relevant files:**
- `routes/api.php`
- `AuthController.php`
- `config/sanctum.php`

**Complexity:** Medium

---

## Milestone 4 — Project Entity — If Required for the MVP

**Goal:** Group tasks under projects.

**Existing file:** `app/Models/Project.php`, currently empty

**Tasks:**
1. Create a `projects` migration with `name`, `description`, and `user_id`
2. Add a `project_id` foreign key to `tasks`
3. Implement Project CRUD for the web and, optionally, the API
4. Add task filtering by project

**Dependencies:** Milestone 0 and Milestone 2

**Complexity:** Medium

---

## Milestone 5 — Tests and Code Quality

**Goal:** Add coverage for the project's domain behavior.

**Tasks:**
1. Add `TaskApiTest`, `TaskWebTest`, and `RoleApiTest`
2. Fix `StatusFactory` and `TaskFactory`
3. Add PHPStan and Laravel Pint if desired

**Complexity:** Medium

---

## Milestone 6 — Documentation and Deployment

**Goal:** Prepare the application for deployment and handoff.

**Tasks:**
1. Update `README.md`
2. Add Docker, Laravel Sail, or a clear deployment guide
3. Add `.env.production.example`
4. Add a GitHub Actions CI pipeline

**Complexity:** Small to medium

---

## Milestone 7 — Optional Post-MVP Features

- Due dates
- Comments
- Labels and tags
- Notifications
- Activity log
- Workspace and team support
- Advanced task filtering and search
- File attachments

---

## Recommended Next Task

### Title: **Task Schema Alignment — Add `user_id`, Add `status_id`, and Correct `TasksController`**

### Technical Description

The mismatch between the migrations, models, and controller is the largest blocker in the repository. `Task` defines `user()` and `status()` relationships, but the corresponding foreign keys do not exist. The `tasks.status` field is an integer even though a separate `statuses` table is seeded. The API also returns every task without user-level scoping.

### Exact Scope

**In scope:**
- Create a migration adding `user_id` and `status_id` to `tasks`
- Remove or migrate the legacy integer `status` column
- Update the `Task` and `Status` models
- Update `TasksController` for ownership scoping, validation, and response formatting
- Update `TaskFactory` and `DatabaseSeeder`
- Add Task API feature tests

**Out of scope:**
- Blade or frontend UI
- Project entity
- Comments, due dates, and priorities
- Roles and permissions, which belong to Milestone 1
- Removing API authentication or unifying web and API authentication

### Acceptance Criteria

1. `tasks.user_id` references `users.id`
2. `tasks.status_id` references `statuses.id`
3. `POST /api/tasks` with a valid token creates a task with the correct `user_id`
4. `GET /api/tasks` returns only the authenticated user's tasks
5. `status_id` validation uses `exists:statuses,id`
6. `php artisan migrate:fresh --seed` completes without errors
7. At least four Task API feature tests pass

### Files to Review or Change

| Action | Path |
|--------|------|
| Create | `database/migrations/xxxx_add_user_id_status_id_to_tasks_table.php` |
| Modify | `app/Models/Task.php` |
| Modify | `app/Models/Status.php` |
| Modify | `app/Http/Controllers/TasksController.php` |
| Modify | `database/factories/TaskFactory.php` |
| Modify | `database/seeders/DatabaseSeeder.php` |
| Create | `tests/Feature/TaskApiTest.php` |

### Tests That Should Pass

- All existing 25 tests, with no regressions
- New `TaskApiTest` cases for create, scoped list, update, delete, and unauthenticated access returning HTTP 401

### Definition of Done

- [ ] The migration has been applied and the schema matches the Eloquent models
- [ ] Manual API testing succeeds using curl or Postman
- [ ] `php artisan test` reports all tests passing
- [ ] `docs/DATABASE_SCHEMA.md` and `docs/FEATURE_STATUS.md` are updated in the following pull request

---

## Overall Priority Order

```text
M0 Schema Fix → M1 Authorization → M2 Task UI → M3 Authentication Strategy → M4 Projects → M5 Tests → M6 Deployment → M7 Optional Features
```
