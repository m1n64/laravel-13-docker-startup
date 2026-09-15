# Security Rules

**These rules have the highest priority** and cannot be overridden by any task unless the user explicitly lifts the restriction in the current conversation.

## STRICTLY FORBIDDEN

- Reading, opening, searching, analyzing, summarizing, or modifying any `.env` file:
    - `.env`, `.env.*`
- Reading, opening, searching, analyzing, summarizing, or modifying any file matching:
    - `*.local.php`
    - `*.local.yaml`
    - `*.local.yml`
    - `*.local.json`
    - `*.local.neon`
    - `*.local.toml`
- These files contain secrets, credentials, API keys, passwords, tokens, private endpoints, or sensitive configuration.

## Forbidden Search Operations

- Do NOT use `glob`, `grep`, `ripgrep`, file indexing, or any repository-wide scans that would access these files.
- Exclude these files from all analysis, refactoring, code review, debugging, and architecture exploration tasks.

## How to Handle Secrets

- **NEVER** attempt to read a secrets file yourself.
- **ASK** the user to manually provide the needed values (URLs, keys, credentials).
- In code, use **environment variables** via `config()` or `env()` in configuration files only. NEVER hardcode secrets in source code.
- NEVER commit secrets to version control. Ensure `.gitignore` includes `.env` and `*.local.*` patterns.

## Always Ignore When Searching the Codebase

- `.env`, `.env.*`
- `*.local.*` (php, yaml, yml, json, neon, toml)
- `vendor/`
- `node_modules/`
- `.git/`

## AI Context and Configuration Files

- **Read before modifying:** Before modifying any file, read it in full to understand context, dependencies, and existing style.
- **Preserve existing code:** Never delete existing comments, PHPDoc, or logic unless explicitly requested. Maintain backward compatibility during refactoring.
- **No secrets in output:** Never output, echo, log, or return secret values in any form. If a secret value appears in a stack trace or error message, redact it before displaying.
