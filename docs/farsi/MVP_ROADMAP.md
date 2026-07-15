# MVP Roadmap — Task Manager

> نقشه راه بر اساس وضعیت **واقعی** این repository — نه یک roadmap عمومی.

---

## وضعیت فعلی (خلاصه)

- Laravel 12 + Breeze (web auth) + Sanctum (API auth) + Spatie Permission
- Task API نیمه‌کاره با schema ناهماهنگ
- UI محصول (task management) وجود ندارد
- تخمین پیشرفت MVP: **~22%**

### نحوه محاسبه

23 حوزه MVP ارزیابی شد. به هر حوزه یک درصد اختصاص داده شد (Done=100, Mostly done=75, In progress=50, Started=25, Not started=0). میانگین: **~22%**. این یک **تخمین مهندسی** است نه معیار دقیق محصول.

---

## Milestone 0 — تثبیت پایه (Blocking Fixes)

**هدف:** رفع ناهماهنگی‌های schema و controller که بقیه کار را متوقف می‌کنند.

**چرا الان:** بدون schema درست، assignment، status workflow، و authorization قابل اتکا نیستند.

**فایل‌های موجود:**
- `database/migrations/2026_05_29_085546_create_tasks_table.php`
- `database/migrations/2026_05_29_091832_create_statuses_table.php`
- `app/Models/Task.php`, `app/Models/Status.php`
- `app/Http/Controllers/TasksController.php`
- `database/seeders/DatabaseSeeder.php`

**فایل‌های جدید/تغییر:**
- migration جدید: `add_user_id_and_status_id_to_tasks_table`
- `Task.php`, `Status.php`, `TasksController.php`
- `TaskFactory.php`, `DatabaseSeeder.php`

**Tasks:**
1. اضافه کردن `user_id` (FK → users) به `tasks`
2. تبدیل `tasks.status` (integer) به `status_id` (FK → statuses)
3. به‌روزرسانی `$fillable`, relationships, validation
4. در `addTask`/`editTask`: set کردن `user_id` از `auth()->id()` (Sanctum)
5. اصلاح `getAll` به `auth()->user()->tasks()` یا scope مناسب
6. اصلاح response format در `getAll` (حذف آرایه اضافی)
7. حذف import بلااستفاده `TaskListExtension`
8. اضافه کردن `color` به `Status::$fillable`
9. seed کردن چند task نمونه

**Dependencies:** هیچ — اولین milestone

**Acceptance criteria:**
- [ ] `php artisan migrate:fresh --seed` بدون خطا
- [ ] `Task::with(['user','status'])` روابط را load می‌کند
- [ ] API create task، `user_id` و `status_id` صحیح set می‌کند
- [ ] `GET /api/tasks` فقط taskهای کاربر جاری را برمی‌گرداند
- [ ] Response JSON ساختار یکنواخت دارد

**Tests پیشنهادی:**
- `tests/Feature/TaskApiTest.php` — CRUD + ownership isolation

**Complexity:** Medium

**Risks:**
- Migration روی دیتای موجود (اگر production data باشد)
- تصمیم: آیا `completed` boolean جدا از status لازم است؟

---

## Milestone 1 — تکمیل RolesController و Authorization پایه

**هدف:** Spatie Permission را عملیاتی کنید.

**چرا بعد از M0:** taskها user-scoped هستند؛ حالا می‌توان permission تعریف کرد.

**فایل‌های موجود:**
- `app/Http/Controllers/RolesController.php`
- `config/permission.php`
- `app/Models/User.php` (`HasRoles`)

**Tasks:**
1. تکمیل `RolesController@addRole` — `Role::create(['name' => ..., 'guard_name' => 'web'])`
2. Seed roles: `admin`, `member`
3. اختصاص role به user seed
4. Middleware `permission:` یا Policy روی Task endpoints
5. تصمیم guard: `web` vs `sanctum`

**Acceptance criteria:**
- [ ] `POST /api/roles` role ایجاد و JSON برمی‌گرداند
- [ ] کاربر بدون permission نمی‌تواند task دیگران را ببیند/ویرایش کند

**Tests:** Role creation + unauthorized access 403

**Complexity:** Medium

**Risks:** Guard mismatch بین Breeze (web) و API (sanctum)

---

## Milestone 2 — Task Management UI (Web)

**هدف:** اولین تجربه کاربری واقعی MVP — مدیریت task از مرورگر.

**چرا الان:** API آماده است؛ بزرگترین gap محصول UI است.

**فایل‌های موجود:**
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/navigation.blade.php`
- Breeze components در `resources/views/components/`

**فایل‌های جدید:**
- `app/Http/Controllers/TaskWebController.php` (یا توسعه TasksController)
- `resources/views/tasks/index.blade.php`
- `resources/views/tasks/create.blade.php`
- `resources/views/tasks/edit.blade.php`
- `routes/web.php` — resource routes
- FormRequest: `StoreTaskRequest`, `UpdateTaskRequest`

**Tasks:**
1. تصمیم: web routes با session auth (نه Sanctum) — **توصیه‌شده**
2. CRUD blade pages با Tailwind/Alpine
3. لینک Tasks در navigation
4. Dashboard: خلاصه taskها (count by status)
5. Status dropdown از `Status::all()`

**Acceptance criteria:**
- [ ] کاربر login‌شده می‌تواند task بسازد، ببیند، ویرایش و حذف کند
- [ ] فقط taskهای خودش نمایش داده می‌شود
- [ ] Dashboard آمار ساده نشان می‌دهد

**Tests:** `TaskWebTest.php` — browser-like feature tests

**Complexity:** Large

**Risks:** دو مسیر API + Web — نیاز به استراتژی auth یکپارچه

---

## Milestone 3 — یکپارچه‌سازی Authentication

**هدف:** حذف دوگانگی auth یا مستندسازی آگاهانه dual-mode.

**گزینه‌ها:**
- **A:** فقط web session — API را حذف یا محدود کنید
- **B:** SPA-ready — Sanctum stateful + یک frontend
- **C:** dual-mode با مستندات واضح

**فایل‌ها:** `routes/api.php`, `AuthController.php`, `config/sanctum.php`

**Complexity:** Medium

---

## Milestone 4 — Project Entity (اگر در MVP لازم است)

**هدف:** گروه‌بندی taskها زیر Project.

**فایل موجود:** `app/Models/Project.php` (خالی)

**Tasks:**
1. migration `projects` (name, description, user_id)
2. `project_id` FK روی tasks
3. CRUD projects (web + optionally API)
4. فیلتر task by project

**Dependencies:** M0, M2

**Complexity:** Medium

---

## Milestone 5 — Tests و کیفیت

**هدف:** پوشش تست برای domain logic.

**Tasks:**
1. `TaskApiTest`, `TaskWebTest`, `RoleApiTest`
2. Factory fixes (`StatusFactory`, `TaskFactory`)
3. PHPStan/Pint (اختیاری)

**Complexity:** Medium

---

## Milestone 6 — Documentation و Deployment

**هدف:** آماده‌سازی برای deploy.

**Tasks:**
1. به‌روزرسانی `README.md`
2. Docker/Sail یا deploy guide
3. `.env.production.example`
4. CI pipeline (GitHub Actions)

**Complexity:** Small–Medium

---

## Milestone 7 — Features اختیاری (Post-MVP)

- Due dates
- Comments
- Labels/tags
- Notifications
- Activity log
- Workspace/team
- Task filtering/search پیشرفته
- File attachments

---

## 🎯 Recommended Next Task

### عنوان: **Task Schema Alignment — user_id, status_id, و اصلاح TasksController**

### توضیح فنی

ناهماهنگی بین migration، model، و controller بزرگترین مانع پیشرفت است. `Task` روابط `user()` و `status()` دارد ولی FK در دیتابیس نیست. `tasks.status` integer است در حالی که جدول `statuses` جدا seed شده. API بدون user scoping همه taskها را برمی‌گرداند.

### Scope (دقیق)

**In scope:**
- Migration جدید برای `user_id` و `status_id` روی `tasks`
- حذف/تبدیل ستون `status` (integer) قدیمی
- به‌روزرسانی `Task`, `Status` models
- به‌روزرسانی `TasksController` (scoping, validation, response format)
- به‌روزرسانی `TaskFactory`, `DatabaseSeeder`
- تست‌های Feature برای Task API

**Out of scope:**
- UI/Blade pages
- Project entity
- Comments, due dates, priorities
- Roles/Permissions (Milestone 1)
- حذف API auth یا یکپارچه‌سازی web auth

### Acceptance Criteria

1. Schema: `tasks.user_id` → `users.id`, `tasks.status_id` → `statuses.id`
2. `POST /api/tasks` با token معتبر task با `user_id` صحیح می‌سازد
3. `GET /api/tasks` فقط taskهای authenticated user را برمی‌گرداند
4. Validation: `status_id` باید `exists:statuses,id` باشد
5. `migrate:fresh --seed` بدون خطا
6. حداقل 4 تست Feature برای Task API pass می‌شوند

### فایل‌های مورد بررسی/تغییر

| Action | Path |
|--------|------|
| Create | `database/migrations/xxxx_add_user_id_status_id_to_tasks_table.php` |
| Modify | `app/Models/Task.php` |
| Modify | `app/Models/Status.php` |
| Modify | `app/Http/Controllers/TasksController.php` |
| Modify | `database/factories/TaskFactory.php` |
| Modify | `database/seeders/DatabaseSeeder.php` |
| Create | `tests/Feature/TaskApiTest.php` |

### Tests that should pass

- Existing 25 tests (no regression)
- New: `TaskApiTest` — create, list (scoped), update, delete, 401 without token

### Definition of Done

- [ ] Migration اجرا شده و schema با models هم‌خوان است
- [ ] API manual test موفق (curl/Postman)
- [ ] `php artisan test` — all green
- [ ] `docs/DATABASE_SCHEMA.md` و `docs/FEATURE_STATUS.md` به‌روز شده (در PR بعدی)

---

## ترتیب اولویت کلی

```
M0 Schema Fix → M1 Authorization → M2 Task UI → M3 Auth unify → M4 Projects → M5 Tests → M6 Deploy → M7 Optional
```
