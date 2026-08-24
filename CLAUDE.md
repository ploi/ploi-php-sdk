# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

PHP SDK for the Ploi.io server management API. Wraps the REST API with a fluent, chainable interface using Guzzle HTTP.

## Commands

```bash
# Install dependencies
composer install

# Run the mocked unit suite (no network, no credentials) - this is what CI runs
composer test
# or: vendor/bin/phpunit --testsuite unit

# Run the live-API suite (requires tests/.env with API_TOKEN - see tests/.env.sample)
composer test:integration

# Run both
composer test:all

# Run a single test file
vendor/bin/phpunit tests/Unit/Resources/ServerTest.php

# Run a single test method
vendor/bin/phpunit --filter testListsServers

# Code standards (PSR-2)
composer standards
# or: vendor/bin/phpcs --standard=PSR12 --colors src

# Static analysis (PHPStan level 5)
vendor/bin/phpstan analyse -c phpstan.neon
```

## Architecture

**Entry point:** `Ploi\Ploi` - holds the API token, Guzzle client, and `makeAPICall()` which handles all HTTP requests and maps status codes to typed exceptions (401→Unauthenticated, 404→NotFound, etc.).

**Resource hierarchy** - resources are chained fluently in a parent-child pattern:

```
Ploi → Server → Site → [Certificate, Repository, Queue, Deployment, App, Environment, Alias, ...]
Ploi → Server → [Database, Cronjob, Daemon, SshKey, Service, NetworkRule, SystemUser, Opcache, ...]
Ploi → [Project, Script, StatusPage, User, WebserverTemplate, FileBackup]
```

**Base class:** `Ploi\Resources\Resource` - abstract base for all resources. Holds references to the Ploi client, parent server/site/database, endpoint string, and resource ID. Provides `setId()`/`setIdOrFail()` for ID validation.

**Key patterns:**
- Resources build their API endpoint string from their parent chain (e.g., `servers/{id}/sites/{id}/certificates`)
- `Server` has its own `buildEndpoint()` and `callApi()` helper; child resources like `Site` construct endpoints by reading the parent server's endpoint
- Methods accepting an optional `$id` parameter will use it or fall back to the previously set ID via `setId()`
- API options are passed as `['body' => json_encode([...])]` to Guzzle
- `Ploi\Http\Response` wraps Guzzle's ResponseInterface with `getJson()`, `getData()`, and `toArray()`

**Traits:**
- `HasPagination` - adds `page($pageNumber, $perPage)` and `perPage()` for paginated list endpoints
- `HasHistory` - debug trail tracking actions on a resource
- `HasSearch` - search functionality for resources

## Testing

Two suites, split by whether they touch the network.

**`tests/Unit/`** - the default suite and the one CI runs. Tests extend `Tests\Unit\TestCase`, which builds a real `Ploi` client but injects a Guzzle `MockHandler` through `Ploi::setHandler()`. Because the client itself is still built by production code, the base URI, headers and `http_errors` setting under test are the ones users get. Helpers: `queue()` to push a JSON response, `assertRequest($method, $path, $body)` to assert the verb, full URI and decoded body of a recorded request.

**`tests/Integration/`** - hits the live API. Tests extend `Tests\Integration\TestCase`, which loads `tests/.env` via phpdotenv and needs a real API token. Excluded from the default suite; these create, restart and delete real resources.

## Code Style

PSR-12 standard. PSR-4 autoloading for `src/` (namespace `Ploi\` maps to `src/Ploi/`). All source files use `declare(strict_types=1)`.
