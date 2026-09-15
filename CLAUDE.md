# CLAUDE.md — Instructions for Claude

## ATTENTION: Mandatory Reading

**Before any work on the code, you MUST read the following files in this exact order:**

1. `.agents/SECURITY.md`
2. `.agents/ARCHITECTURE.md`
3. `.agents/PHP.md`
4. `.agents/NAMING.md`
5. `.agents/LARAVEL.md`
6. `.agents/API.md`
7. `.agents/MODULES.md`
8. `.agents/TESTING.md`

If a relevant file is not listed above, still read it. The `.agents/` directory is the single source of truth. Never assume conventions that are not written there.

## Critical Rules (Highest Priority)

You are not allowed to ignore these under any circumstances.

1. **Security:** NEVER read `.env`, `.env.*`, `*.local.*`, or any file under `vendor/`/`node_modules/` that may contain secrets. Full rules: `.agents/SECURITY.md`.
2. **PHP 8.5 + Strict Types:** Every PHP file starts with `declare(strict_types=1);`. Full rules: `.agents/PHP.md`.
3. **Clean Architecture:** Use `Domain` / `Application` / `Infrastructure` / `Transport`. No facades, no magic, no static access, strict constructor DI. Full rules: `.agents/ARCHITECTURE.md`.
4. **Naming & Documentation:** PSR-12, strict PHPDoc on every class/interface/trait/property/method, intention-revealing names. Full rules: `.agents/NAMING.md`.
5. **Laravel 13:** No global helpers, no static `Yii::` or `Auth::` calls, route attributes, OpenAPI docs in `Transport/Http/Doc`. Full rules: `.agents/LARAVEL.md` and `.agents/API.md`.
6. **Optional Modules:** See `.agents/MODULES.md` before creating or editing anything in `modules/`. A module is a full Composer package, uses `Module\\<Name>` namespace, exposes only `Boundary` interfaces, never creates itself by default.
7. **Tests:** Before finishing any task, run `make test`. Full rules: `.agents/TESTING.md`.

## What to Read for Each Task

| Task type | Files to read |
|---|---|
| Any new PHP class or method | `.agents/PHP.md`, `.agents/NAMING.md` |
| New or changed architecture | `.agents/ARCHITECTURE.md` |
| Controllers, routes, middleware, providers | `.agents/LARAVEL.md`, `.agents/API.md` |
| OpenAPI / swagger-php | `.agents/API.md` |
| Database / migrations | `.agents/MIGRATIONS.md`, `.agents/LARAVEL.md` |
| Tests | `.agents/TESTING.md` |
| New module in `modules/` | `.agents/MODULES.md` first, then others |

## Project Context

- **Stack:** PHP 8.5, Laravel 13, PostgreSQL, Docker.
- **Architecture:** Clean Architecture (`Domain` / `Application` / `Infrastructure` / `Transport`).
- **Structure:**
  - Core code: `app/`
  - Local packages: `packages/`
  - Optional modules: `modules/`
- **DI:** Constructor injection only; providers in `app/Infrastructure/Provider/` and `bootstrap/providers.php`.
- **Routing:** `spatie/laravel-route-attributes` discovers `app/Transport/Http/Controller` and `modules/*/src/Transport/Http/Controller`.

## Module Rules

- A module is a full Composer package with namespace `Module\<Name>`, Boundary facades via interfaces, no static access.
- You MUST read `.agents/MODULES.md` before creating or editing any module.
- You MUST NOT create a module unless the user explicitly asks for it.
- A module-specific `AGENTS.md` inside a module directory has priority over root rules.
