# Open Questions — Task Manager

> فقط سؤالاتی که **از روی کد قابل پاسخ نیستند** و نیاز به تصمیم شما دارند.

---

## 1. معماری محصول

### Q1: MVP باید Web-first باشد یا API-first؟

**Context:** الان دو مسیر جدا وجود دارد — Breeze (session) و Sanctum (token). UI برای task وجود ندارد؛ API نیمه‌کاره است.

**گزینه‌ها:**
- **Web-first:** تمرکز روی Blade pages با session auth؛ API بعداً
- **API-first:** تکمیل REST API؛ frontend جدا (Vue/React/mobile)
- **Hybrid:** هر دو، با مستندات واضح

**تأثیر:** مسیر Milestone 2 و 3، حجم کار، انتخاب auth strategy

---

## 2. دامنه MVP — Project لازم است؟

**Context:** مدل `Project` خالی وجود دارد ولی migration و feature نیست. Taskها مستقل هستند.

**سؤال:** آیا در MVP باید taskها زیر Project گروه‌بندی شوند، یا taskهای مستقلِ user-level کافی است؟

**تأثیر:** Milestone 4، schema (`project_id` on tasks), UI complexity

---

## 3. Workspace / Team

**Context:** هیچ entity برای team/workspace وجود ندارد. Spatie Permission با `teams` غیرفعال است (`config/permission.php`).

**سؤال:** MVP تک‌کاربره است (هر user taskهای خودش) یا چند کاربره با team مشترک؟

**تأثیر:** authorization model، task visibility، کل معماری

---

## 4. Workflow وضعیت Task (Status)

**Context:** 4 status seed شده: `to do`, `in Progress`, `completed`, `hold`. فیلد `completed` boolean هم جداگانه روی task هست.

**سؤالات:**
- آیا `completed` boolean باید حذف شود و فقط `status_id` کافی است؟
- آیا transition بین statusها محدود است (مثلاً `hold` → `completed` ممنوع)؟
- آیا statusها توسط admin قابل تعریف هستند یا ثابت؟

---

## 5. اولویت (Priority) در MVP

**Context:** فیلد priority وجود ندارد.

**سؤال:** آیا priority (مثلاً low/medium/high) بخش MVP است؟

---

## 6. Due Date

**سؤال:** آیا مهلت انجام (due_date) در MVP لازم است؟ اگر بله، آیا notification برای overdue هم لازم است؟

---

## 7. نقش‌ها و دسترسی‌ها (RBAC)

**Context:** Spatie Permission نصب شده؛ `RolesController@addRole` ناقص. هیچ permission روی task endpoints نیست.

**سؤالات:**
- چه roleهایی لازم است؟ (admin, member, viewer؟)
- آیا admin همه taskها را می‌بیند؟
- آیا task فقط توسط assignee قابل ویرایش است یا creator هم؟

---

## 8. Email Verification

**Context:** Dashboard middleware `verified` دارد ولی `User` از `MustVerifyEmail` implement نمی‌کند (comment شده). تست‌های verification pass می‌شوند چون routeها مستقل کار می‌کنند.

**سؤال:** آیا email verification اجباری است در MVP؟

---

## 9. دیتابیس Production

**Context:** `.env.example` پیش‌فرض SQLite است. محیط dev شما ممکن است MySQL باشد (migrate کند بود).

**سؤال:** دیتابیس production چیست؟ (SQLite, MySQL, PostgreSQL)

**تأثیر:** migration syntax, hosting, backup strategy

---

## 10. UI/UX Expectations

**سؤالات:**
- آیا Kanban board لازم است یا لیست ساده کافی است؟
- آیا drag-and-drop برای تغییر status لازم است؟
- زبان UI: انگلیسی، فارسی، یا هر دو؟

---

## 11. Deployment Target

**Context:** بدون Docker, CI/CD, Nginx config.

**سؤال:** deploy کجا انجام می‌شود؟ (shared hosting, VPS, Laravel Forge, Vapor, Docker)

---

## 12. حساب Demo

**Context:** Seeder یک user می‌سازد: `arta@gmail.com` / `1234`.

**سؤال:** آیا این credentials برای demo/production قابل قبول است؟ (امنیت)

---

## سؤالاتی که از کد پاسخ داده شدند (نیازی به تصمیم نیست)

| سؤال | پاسخ از کد |
|------|------------|
| Laravel version? | v12.59.0 |
| Auth package? | Breeze (web) + Sanctum (API) |
| Permission package? | spatie/laravel-permission ^6.25 |
| Task UI exists? | No |
| Task API works? | Partially — CRUD yes, schema issues |
| Tests pass? | Yes — 25 auth/profile tests |
| Project migration exists? | No |
| Comments/attachments exist? | No |
