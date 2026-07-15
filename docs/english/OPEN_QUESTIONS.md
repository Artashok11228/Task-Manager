# Open Questions — Task Manager

> This document contains only questions that **cannot be answered from the codebase** and require a product or technical decision.

---

## 1. Product Architecture

### Q1: Should the MVP be web-first or API-first?

**Context:** The application currently has two separate authentication paths: Breeze for session-based web authentication and Sanctum for token-based API authentication. No Task UI exists, and the API is incomplete.

**Options:**
- **Web-first:** Focus on Blade pages and session authentication; postpone the API
- **API-first:** Complete the REST API and build a separate Vue, React, or mobile frontend
- **Hybrid:** Support both, with clear architecture and documentation

**Impact:** Milestones 2 and 3, implementation scope, and authentication strategy

---

## 2. MVP Scope — Is `Project` Required?

**Context:** An empty `Project` model exists, but there is no migration or implemented feature. Tasks currently exist independently.

**Question:** Should tasks be grouped under projects in the MVP, or are standalone user-level tasks sufficient?

**Impact:** Milestone 4, the addition of `project_id` to the Task schema, and UI complexity

---

## 3. Workspace and Team Support

**Context:** No team or workspace entity exists. The Spatie Permission `teams` option is disabled in `config/permission.php`.

**Question:** Is the MVP single-user, where each user manages only their own tasks, or collaborative, where multiple users share tasks within a team?

**Impact:** Authorization model, task visibility, tenancy boundaries, and the overall architecture

---

## 4. Task Status Workflow

**Context:** Four statuses are seeded: `to do`, `in Progress`, `completed`, and `hold`. Tasks also contain a separate `completed` boolean field.

**Questions:**
- Should the `completed` boolean be removed so that `status_id` becomes the single source of truth?
- Should transitions between statuses be restricted, for example preventing `hold` → `completed`?
- Should statuses be fixed, or configurable by an administrator?

---

## 5. Priority in the MVP

**Context:** No priority field exists.

**Question:** Is task priority, such as `low`, `medium`, and `high`, required for the MVP?

---

## 6. Due Dates

**Question:** Is a task due date required for the MVP? If yes, are overdue notifications also required?

---

## 7. Roles and Access Control — RBAC

**Context:** Spatie Permission is installed, but `RolesController@addRole` is incomplete and Task endpoints do not enforce permissions.

**Questions:**
- Which roles are required: `admin`, `member`, `viewer`, or others?
- Should an administrator be able to view every task?
- Can only the assignee edit a task, or should the creator also be allowed to edit it?

---

## 8. Email Verification

**Context:** The dashboard uses the `verified` middleware, but the `User` model does not implement `MustVerifyEmail` because the interface is commented out. Verification-related tests pass because the routes exist independently.

**Question:** Should email verification be mandatory in the MVP?

---

## 9. Production Database

**Context:** `.env.example` uses SQLite by default. The development environment may use MySQL, based on observed migration behavior.

**Question:** Which database will be used in production: SQLite, MySQL, or PostgreSQL?

**Impact:** Migration behavior, hosting requirements, backup strategy, and operational tooling

---

## 10. UI and UX Expectations

**Questions:**
- Is a Kanban board required, or is a simple list sufficient?
- Is drag-and-drop status updating required?
- Should the interface be English, Persian, or bilingual?

---

## 11. Deployment Target

**Context:** The repository currently contains no Docker, CI/CD, or Nginx configuration.

**Question:** Where will the application be deployed: shared hosting, a VPS, Laravel Forge, Laravel Vapor, or Docker-based infrastructure?

---

## 12. Demo Account

**Context:** The seeder creates a user with `arta@gmail.com` and the password `1234`.

**Question:** Are these credentials acceptable for local demonstration only, and how should demo or production credentials be managed securely?

---

## Questions Already Answered by the Codebase

| Question | Answer from the codebase |
|----------|--------------------------|
| Which Laravel version is used? | Laravel v12.59.0 |
| Which authentication packages are used? | Breeze for web authentication and Sanctum for API authentication |
| Which permission package is installed? | `spatie/laravel-permission` ^6.25 |
| Does a Task UI exist? | No |
| Does the Task API work? | Partially; CRUD exists, but schema and behavior are inconsistent |
| Do the current tests pass? | Yes; 25 authentication and profile tests pass |
| Does a Project migration exist? | No |
| Are comments or attachments implemented? | No |
