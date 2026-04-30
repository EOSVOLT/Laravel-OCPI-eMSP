# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 12 / PHP 8.4 package (`eosvolt/laravel-ocpi-emsp`) implementing the OCPI 2.2.1 protocol for **both** roles — eMSP (e-Mobility Service Provider) and CPO (Charging Point Operator). It is consumed by host Laravel applications via auto-discovery of `Ocpi\OcpiServiceProvider`. There is no app skeleton in this repo — only the package source.

Modules implemented: CDRs, Commands, Credentials, Locations, Sessions, Tariffs, Tokens, Versions.

## Common commands

```bash
# Install / update dependencies
composer install

# Run the full test suite (PHPUnit)
vendor/bin/phpunit

# Run a single test file or method
vendor/bin/phpunit src/Tests/Unit/PartyTest.php
vendor/bin/phpunit --filter test_method_name src/Tests/Unit/PartyTest.php

# Format code (Laravel Pint — only code-style tool configured)
composer format
# or:
vendor/bin/pint
```

Tests live in `src/Tests/` (note: **not** `tests/`), configured via `phpunit.xml` as the "Package Test Suite". The PHPUnit env runs against an in-memory sqlite DB. `Ocpi\Tests\TestCase` extends bare `PHPUnit\Framework\TestCase` — there is no Orchestra Testbench, so tests do not boot a Laravel app by default. Treat existing tests under `src/Tests/Unit/` as the pattern.

The OCPI artisan commands (registered in `OcpiServiceProvider::registerCommands`) are only runnable from a host Laravel app that requires this package — not from this repo directly. They include:

- `ocpi:versions:update {party_code}` — fetch versions/details, store endpoints
- `ocpi:sender:credentials:initialize|register|update {party_code}` — Sender-side credentials handshake
- `ocpi:receiver:credentials:initialize` — Receiver-side credentials initialization
- `ocpi:locations:synchronize` — pull/push locations
- `ocpi:tariffs:synchronize` — tariffs sync

## Architecture

### Two halves: Server (inbound) and Client (outbound)

Every OCPI module is symmetric — it ships both an HTTP server (routes/controllers/requests under `Server/`) and a Saloon-based HTTP client (under `Client/`). When working on a module, expect to touch both sides.

```
src/Modules/<Module>/
  Server/   Controllers, Endpoints/{CPO,EMSP}/<version>.php, Requests
  Client/   V2_2_1/Resource.php and request classes
  Console/  Commands/  (artisan commands the host app registers)
  Objects/  OCPI DTOs
  Events/   Domain events
  Factories/ Eloquent model factories
```

### Server-side routing (how inbound routes are mounted)

`OcpiServiceProvider::loadRoutes` only mounts routes when `ocpi.server.enabled = true`, and mounts CPO routes only if `ocpi-cpo.versions` is non-empty (same for `ocpi-emsp.versions`). Routes are loaded from:

- `src/Support/Server/Endpoints/{CPO,EMSP}/common.php` — version-list endpoint, applies `IdentifyParty` + `LogRequest` middleware.
- `src/Support/Server/Endpoints/{CPO,EMSP}/version.php` — iterates configured versions and modules, then recursively loads each module's `src/Modules/<Module>/Server/Endpoints/{CPO,EMSP}/<version>.php` file. Adds `IdentifyEMSPVersion`/`IdentifyCPOVersion` middleware.

URI/name prefixes default to `ocpi/cpo` and `ocpi/emsp` — configurable per-role via `OCPI_*_SERVER_ROUTING_URI_PREFIX` and `..._NAME_PREFIX`. **When adding a new module, you must also add a `Server/Endpoints/{CPO,EMSP}/2.2.1.php` file or the route loop in `version.php` will throw a "file not found" error** since it loads each entry in `versions[].modules` unconditionally.

### Client-side (outbound calls)

`Ocpi\Support\Client\Client` is a Saloon `Connector` that all outbound OCPI calls go through. It is constructed with a `PartyToken` plus a `module` string and resolves the base URL from one of:

- `versions.information` → the party role's discovered URL
- `versions.details` → the party's stored `version_url`
- otherwise → `partyToken->party_role->endpoints[$module][$interfaceRole->value]` (populated during the credentials/versions handshake)

`SenderClient` and `ReceiverClient` are thin subclasses that pin the `interfaceRole`. Each module exposes a `Resource` accessor on the connector (`->locations()`, `->cdrs()`, etc.) that returns a Saloon resource wrapping the request classes. Auth always uses `TokenAuthenticator` with the token base64-encoded for OCPI ≥ 2.2 (handled by `GeneratorHelper::encodeToken`).

**Hub support** is built into `Client::getHeadersForHub`: when the target `PartyRole` has `role = HUB`, the client automatically injects `OCPI-to-party-id`, `OCPI-to-country-code`, `OCPI-from-party-id`, `OCPI-from-country-code` headers using the parent role. Don't add these headers manually.

### Database

Migrations auto-load from `src/Data/Migrations` via `loadMigrationsFrom`. All OCPI tables share the prefix `ocpi_` (configurable via `OCPI_DATABASE_TABLE_PREFIX`). The DB connection is independently configurable via `OCPI_DATABASE_CONNECTION` and falls back to the host app's default. Code paths that wrap operations in transactions explicitly use `DB::connection(config('ocpi.database.connection'))->beginTransaction()` — match this pattern when adding new commands/actions, never plain `DB::beginTransaction()`.

**Never run `migrate:fresh` (or `migrate:fresh --seed`) against the host app's dev database.** It drops every table — including non-OCPI tables owned by the host app — and wipes local work. To re-apply a not-yet-shipped migration on the current branch, use `migrate:rollback` followed by `migrate`. The PHPUnit env uses an in-memory sqlite DB that is reconstructed per run, so a `migrate:fresh` inside test setup is fine; do not invoke it manually outside of tests.

Key models:
- `Party` — an OCPI counterparty, identified by `code`. Has `parent_id` for child parties under a hub. `tokens` are `PartyToken` records. `roles` are `PartyRole` records (one party can have CPO and eMSP roles).
- `PartyRole` — a (role, party_id, country_code, code) tuple with its own `endpoints` JSON and (since `2025_10_30`) its own URL. Roles: `CPO`, `EMSP`, `HUB` (see `Ocpi\Support\Enums\Role`).
- `PartyToken` — credentials. `registered=true` once handshake completes. The token is stored decoded in DB and encoded on the wire.
- `JoinParty` — links between parties, see `2026_03_06_..._create_table_join_parties.php`.

### Configuration files

Three configs are merged in `register()` and each is independently publishable:
- `config/ocpi.php` — server enable flag, route prefixes, client base URL, database connection/prefix.
- `config/ocpi-emsp.php` — eMSP party identity + which versions/modules this app exposes as eMSP.
- `config/ocpi-cpo.php` — same shape, for CPO role.

Removing a module from `config/ocpi-emsp.php` `versions['2.2.1']['modules']` removes its inbound routes but leaves outbound client usage intact.

### Logging

A dedicated `ocpi` daily log channel is registered in `OcpiServiceProvider::setLoggingChannel` writing to `storage_path('logs/ocpi.log')`. Tunable via `OCPI_LOG_LEVEL` and `OCPI_LOG_DAILY_DAYS`. Use `Log::channel('ocpi')` rather than the default channel for OCPI request/response logging — the `LogRequest` / `LogResponse` middlewares (both server- and client-side) already do.

### Action pattern

Business logic in commands is delegated to single-method action classes resolved via DI, e.g. `PartyInformationAndDetailsSynchronizeAction::handle($token)`, `SyncPartyRoleAction::handle($parentToken, $credentialsInput)`. Console commands stay thin: validate input, open transaction, call actions, commit/rollback. Follow this when adding new flows.