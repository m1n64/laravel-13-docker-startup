# Migration Workflow

This workflow executes standard database migrations and cleans the cache to ensure the application state is refreshed.

## Steps
1. Run migrations using the Makefile.
2. Clear and cache the application configuration.

## Commands
- make artisan migrate --force
- make artisan optimize:clear
