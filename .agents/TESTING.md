# Testing Protocol

All code MUST be covered by tests. No code is merged unless the tests pass and cover all edge cases defined in the contract.

## Priority: Unit Tests First

**Unit tests are MANDATORY and MUST be written FIRST, before implementation.**

Every Service, Action, and Repository method with business logic MUST have unit tests in `tests/Unit/`.

### Unit Test Requirements

- Test each method in isolation using mocks/stubs for dependencies.
- Cover all branches: success paths, edge cases, and failure paths.
- Test custom exceptions are thrown with correct messages and error codes.
- Name test methods descriptively: `testItCreatesOrderWithValidData`, `testItThrowsExceptionWhenAmountIsNegative`.

## Feature (Functional) Tests

Every API endpoint MUST be covered by functional tests in `tests/Feature/Api/`.

### Feature Test Requirements

Each endpoint requires at minimum:
1. **Success scenario** (201/200) — valid input, correct response structure.
2. **Validation failure** (422) — invalid input, correct error fields.
3. **Authentication/Authorization failure** (401/403) — unauthenticated or unauthorized access.

## Workflow

1. **Write unit tests** for the Service/Action/Repository method.
2. **Write feature tests** for the API endpoint.
3. **Implement** the production code.
4. **Run `make test`** and ensure all tests pass.
5. **Refactor** if needed, then run `make test` once more.

**After every code iteration, the agent MUST run `make test` and ensure all tests pass.**

## Rules

- **Coverage:** Every endpoint needs at least one success test and at least two failure tests (validation & auth). Every Service/Action method needs unit tests covering all branches.
- **Database:** Tests MUST use the real PostgreSQL database via `RefreshDatabase` trait or database transactions. NEVER use in-memory SQLite — the project runs on PostgreSQL and SQLite cannot cover PG-specific features (JSONB, arrays, partial indexes, etc.).
- **Isolation:** Unit tests MUST NOT hit the database. Mock all dependencies. Feature tests MAY hit the database via `RefreshDatabase`.
