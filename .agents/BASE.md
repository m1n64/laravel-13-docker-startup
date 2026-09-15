# Composer Workflow

This workflow executes Composer commands through the project's Makefile to ensure consistency within the Docker environment.

## Steps
- Execute the requested Composer command inside the app container using `make composer`.

## Usage
- Run: /composer [command]
- Example: /composer install

## Commands
- make composer {{args}}
