# Setup Guide — Task Manager

> راهنمای راه‌اندازی بر اساس `composer.json`, `.env.example`, و تست‌های انجام‌شده در 2026-07-12

---

## نرم‌افزارهای مورد نیاز

| نرم‌افزار | نسخه پیشنهادی |
|-----------|---------------|
| PHP | >= 8.2 (extensions: mbstring, xml, pdo, sqlite/mysql, tokenizer, ctype, json, bcmath) |
| Composer | 2.x |
| Node.js | 18+ (برای Vite) |
| npm | 9+ |
| Database | SQLite (پیش‌فرض) یا MySQL/MariaDB |

---

## راه‌اندازی محیط

### 1. Clone و نصب وابستگی‌ها

```bash
composer install
npm install
```

### 2. فایل محیط

```bash
cp .env.example .env
php artisan key:generate
```

یا از اسکریپت composer:

```bash
composer run setup
```

### 3. متغیرهای محیطی مهم (`.env.example`)

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=           # با artisan key:generate پر می‌شود
APP_DEBUG=true
APP_URL=http://localhost

# Database — پیش‌فرض SQLite
DB_CONNECTION=sqlite
# برای MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log
FILESYSTEM_DISK=local
```

> **توجه:** اگر از SQLite استفاده می‌کنید، فایل دیتابیس باید وجود داشته باشد:
> `database/database.sqlite` (اسکریپت `post-create-project-cmd` آن را می‌سازد)

### 4. Migration و Seed

```bash
php artisan migrate
php artisan db:seed
```

یا یکجا:

```bash
php artisan migrate:fresh --seed
```

**داده‌های seed شده** (`DatabaseSeeder`):
- 4 Status: `to do`, `in Progress`, `completed`, `hold`
- 1 User: `arta@gmail.com` / password: `1234` (با cast hashed در مدل)

### 5. Frontend Build

```bash
# Development
npm run dev

# Production build
npm run build
```

### 6. اجرای اپلیکیشن

```bash
# فقط سرور
php artisan serve

# همه سرویس‌ها (سرور + queue + logs + vite)
composer run dev
```

اپلیکیشن روی `http://127.0.0.1:8000` در دسترس است.

---

## Queue / Cache / Storage

| سرویس | Driver پیش‌فرض | نیاز MVP فعلی |
|--------|----------------|---------------|
| Queue | `database` | اختیاری — هیچ Job تعریف نشده |
| Cache | `database` | برای Spatie Permission |
| Session | `database` | بله — برای Breeze auth |
| Mail | `log` | فقط log — ایمیل واقعی ارسال نمی‌شود |
| Storage | `local` | استفاده نشده |

برای queue (در صورت نیاز آینده):

```bash
php artisan queue:work
```

---

## اجرای تست‌ها

```bash
php artisan test
# یا
composer run test
```

**نتیجه تأییدشده (2026-07-12):** 25 تست، 61 assertion — همه pass.  
تست‌ها فقط Breeze auth و profile را پوشش می‌دهند؛ **تست Task API وجود ندارد**.

---

## دسترسی API (Sanctum)

```bash
# 1. Login و دریافت token
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"arta@gmail.com","password":"1234"}'

# 2. استفاده از token
curl http://127.0.0.1:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## مشکلات رایج راه‌اندازی

| مشکل | راه‌حل |
|------|--------|
| `SQLSTATE[HY000] database file does not exist` | `touch database/database.sqlite` |
| Vite assets not found | `npm run build` یا `npm run dev` را اجرا کنید |
| 419 CSRF on web forms | session driver را بررسی کنید؛ `php artisan migrate` برای جدول sessions |
| Permission cache error | `php artisan config:clear` سپس `php artisan migrate` |
| Dashboard redirect به verify-email | کاربر `email_verified_at` ندارد — از `/verify-email` یا factory با `verified` استفاده کنید |

---

## Health Check

```bash
curl http://127.0.0.1:8000/up
```

Route `/up` توسط Laravel 12 به‌صورت پیش‌فرض ثبت شده (`bootstrap/app.php`).
