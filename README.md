# Laravel 13 Docker Startup

A Docker-based project template for **Laravel 13** with **PHP 8.5**, built around **Clean Architecture** and strict coding standards.

> This template is best suited for **API-first projects** and **microservices** where a clean, layered architecture and enterprise-grade coding standards are a good fit.

## Stack

- **PHP 8.5** (FPM on Alpine)
- **Laravel 13**
- **Caddy 2** (web server)
- **PostgreSQL 18**
- **Redis 7**
- **Node.js 24 + NPM**
- **Supervisor** for queues and scheduler
- **Docker + Docker Compose**
- **Xdebug** in dev mode

## Architecture

Core code lives in `app/` and follows **Clean Architecture**:

- `Domain/` — Eloquent models, repository interfaces, enums, exceptions
- `Application/` — services, actions, DTOs
- `Infrastructure/` — repository implementations, providers, external integrations
- `Transport/` — HTTP controllers, requests, resources, OpenAPI doc classes

### Key rules

- Every PHP file starts with `declare(strict_types=1);`
- Constructor injection only, no facades, no global helpers
- Route attributes with `spatie/laravel-route-attributes`
- Controllers are auto-discovered in `app/Transport/Http/Controller` with `api/` prefix and `api` middleware
- OpenAPI documentation classes in `app/Transport/Http/Doc/`
- JSON-only API error responses

## Packages & Features

- `spatie/laravel-route-attributes` — attribute-based routing
- `zircote/swagger-php` + `stoplight-elements` — OpenAPI spec and docs UI at `/api/docs`
- `package/laravel-generators` (local package in `packages/laravel-generators/`) — custom `make:*` commands aligned with Clean Architecture
- `laravel/horizon` — queue dashboard and worker management at `/horizon`
- Optional local modules in `modules/` (full Composer packages, `Module\<Name>` namespace, Boundary facades)

## Quick Start

### 1. Clone and copy environment

```bash
composer create-project m1n64/laravel-13-docker-startup my-project
cd my-project
cp .env.example .env
```

### 2. Configure environment

Edit `.env`:

```dotenv
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=password
```

Optionally rename container and network names in `docker-compose.yml`.

### 3. Start containers

```bash
make up        # dev environment with xdebug/ports
make prod      # prod-like environment
```

### 4. Install dependencies and set up the app

```bash
make composer install
make artisan key:generate
make artisan migrate
make artisan storage:link
```

### 5. Install frontend dependencies (optional)

```bash
make npm install
make npm run dev
```

### 6. Generate API docs

```bash
make docs
```

Then open `http://localhost/api/docs` to view the Stoplight Elements UI.

### 7. Run tests

```bash
make test
```

## Makefile commands

| Action | Command |
|---|---|
| Start dev environment | `make up` |
| Start prod environment | `make prod` |
| Build dev image | `make build` |
| Build prod image | `make prod-build` |
| Stop containers | `make stop` |
| Remove containers and volumes | `make down` |
| Restart all | `make restart` |
| Run artisan | `make artisan <cmd>` |
| Run composer | `make composer <cmd>` |
| Run npm | `make npm <cmd>` |
| Open app shell | `make bash` |
| Open psql | `make psql` |
| Open redis-cli | `make redis` |
| View container logs | `make logs <container>` |
| Run tests | `make test` |
| Generate OpenAPI | `make docs` |

## Custom Generators

The local package `package/laravel-generators` overrides default Laravel `make:*` commands so generated classes land in the right Clean Architecture layer:

```bash
make:controller UserController      # app/Transport/Http/Controller/
make:model User                     # app/Domain/Model/
make:service UserService            # app/Application/Service/
make:action RegisterUser            # app/Application/Action/
make:doc UserDoc                    # app/Transport/Http/Doc/
make:request StoreUserRequest       # app/Transport/Http/Request/
make:resource UserResource          # app/Transport/Http/Resource/
make:exception UserException        # app/Domain/Exception/
make:enum UserStatus                # app/Domain/Enum/
```

## Modules

Optional modules go in `modules/<Name>/`. Each module is a full Composer package with its own `composer.json` and `Module\<Name>` root namespace. They are auto-discovered by Composer via the `modules/*` path repository.

Module structure mirrors `app/`:

```text
modules/Payment/
├── composer.json
├── src/
│   ├── Boundary/          # Public facade + interface
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Transport/
```

See `.agents/MODULES.md` for full rules.

## API Documentation

- OpenAPI JSON: `public/api/openapi.json`
- UI: `http://localhost/api/docs`
- Generate: `make docs`

Doc classes live in `app/Transport/Http/Doc/`, e.g. `app/Transport/Http/Doc/Ping/PingDoc.php`.

## Testing

Run the test suite before finishing any task:

```bash
make test
```

## Xdebug

The dev image includes Xdebug 3. It is configured with `start_with_request=trigger`, so debugging starts only when a session is requested (browser extension, IDE cookie, or `XDEBUG_SESSION=PHPSTORM` query parameter).

### PHPStorm configuration

1. **Settings → PHP → Debug**
   - **Xdebug → Debug port**: `9003`
   - **Xdebug → Can accept external connections**: enabled

2. **Settings → PHP → Servers**
   - Add a server named `stage` (the container has `PHP_IDE_CONFIG="serverName=stage"`).
   - **Host**: `localhost`, **Port**: `80`, **Debugger**: `Xdebug`.
   - Map the project root to `/var/www`:
     - `app/` → `/var/www/app`
     - `packages/` → `/var/www/packages`
     - `modules/` → `/var/www/modules`

3. Click the **"Start Listening for PHP Debug Connections"** button in the toolbar.

### Trigger a debug session

- Browser extension: set `PHPSTORM` as the IDE key and click "Debug".
- Or open any endpoint with the query parameter: `http://localhost/api/ping?XDEBUG_SESSION=PHPSTORM`.

## AI / Agent Guidelines

This project enforces strict standards. If you are an AI agent, read the files in `.agents/` before making any changes:

- `AGENTS.md` / `CLAUDE.md` — entry points
- `ARCHITECTURE.md`, `PHP.md`, `NAMING.md`, `LARAVEL.md`, `API.md`, `MODULES.md`, `TESTING.md`, `SECURITY.md`

## Author

- [m1n64](https://github.com/m1n64)
