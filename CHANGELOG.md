# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2026-03-10

### Breaking Changes

#### Strict types enforced
All source files now declare `strict_types=1`. PHP will throw `TypeError` on type mismatches instead of silently coercing. For example, passing a string `"123"` where an `int` is expected will now fail.

#### `Site::create()` — signature changed
```php
// v1
create(string $domain, string $webDirectory = '/public', string $projectRoot = '/', string $systemUser = 'ploi', ?string $systemUserPassword = null, ?string $webserverTemplate = null, ?string $projectType = null)

// v2
create(string $domain, string $webDirectory = '/public', ?string $projectRoot = null, ?string $systemUser = null, ?int $webserverTemplate = null, ?string $projectType = null, ?string $webhookUrl = null)
```

- **Removed** `$systemUserPassword` — does not exist in the Ploi API.
- **Changed** `$webserverTemplate` type from `string` to `int` — it accepts a webserver template ID.
- **Changed** `$projectRoot` default from `'/'` to `null`.
- **Changed** `$systemUser` default from `'ploi'` to `null`.

**Migration:** Remove any `$systemUserPassword` argument. If you relied on the `$projectRoot` or `$systemUser` defaults, pass them explicitly:
```php
// v1
$ploi->servers(1)->sites()->create('example.com');

// v2 — to preserve v1 behavior
$ploi->servers(1)->sites()->create('example.com', '/public', '/', 'ploi');
```

#### `DatabaseBackup` — endpoint and `create()` signature changed
The endpoint changed from `servers/{id}/databases/{id}/backups` to `backups/database` to match the current Ploi API.

```php
// v1
create(int $interval, string $type, ?string $table_exclusions = null, ?string $locations = null, ?string $path = null)

// v2
create(int $interval, int $backup_configuration, ?array $databases = null, ?string $table_exclusions = null, ?string $locations = null, ?string $path = null, ?int $keep_backup_amount = null, ?string $custom_name = null, ?string $password = null)
```

- **Removed** `$type` — does not exist in the Ploi API.
- **Added** `$backup_configuration` (required) — the ID of your backup configuration from your Ploi profile.
- **Added** `$databases` (optional) — array of database IDs to back up. Defaults to the database from the chain.
- **Added** `$keep_backup_amount`, `$custom_name`, `$password` (optional).
- The `server` ID is automatically passed from the fluent chain.

**Migration:**
```php
// v1
$ploi->servers(1)->databases(2)->backups()->create(60, 'to_server');

// v2
$ploi->servers(1)->databases(2)->backups()->create(60, $backupConfigurationId);
```

### Added

- `Site::clone(int $cloneToServer, ?string $domain)` — clone a site to another server.
- `Site::resetPermissions(?int $id)` — reset file permissions on a site.
- `Site::create()` now accepts `$webhookUrl` to receive a notification when the site has been installed.
- `DatabaseBackup::create()` now supports `$keep_backup_amount`, `$custom_name`, and `$password` parameters.

### Changed

- Migrated autoloading from PSR-0 to PSR-4.
- Updated code standard from PSR-2 to PSR-12.
- Removed obsolete `.travis.yml` (GitHub Actions is used for CI).

### Fixed

- Fixed `StreamInterface` being passed where `string` was expected in exception constructors (`Ploi.php`) and `json_decode()` (`Response.php`).
- `DatabaseBackup` endpoint and parameters now match the current Ploi API documentation.
- `Site::create()` parameters now match the current Ploi API documentation.
