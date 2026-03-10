---
name: Ploi PHP SDK Expert
description: Best practices for using the Ploi PHP SDK to interact with the Ploi.io server management API
compatible_agents:
  - Claude Code
  - Cursor
  - Windsurf
  - GitHub Copilot
tags:
  - php
  - laravel
  - ploi
  - server-management
  - api
  - sdk
---

# Ploi PHP SDK Expert

## Context

This skill covers the **Ploi PHP SDK** (`ploi/ploi-php-sdk`), a PHP wrapper around the [Ploi.io](https://ploi.io) server management REST API. It uses Guzzle HTTP under the hood and provides a fluent, chainable interface for managing servers, sites, databases, deployments, and more.

**Scope:** Initializing the SDK client, chaining resources, performing CRUD operations on all Ploi API resources, handling pagination, error handling, and understanding the resource hierarchy.

## Rules

### Initialization

- Always instantiate the client with an API token: `$ploi = new \Ploi\Ploi($apiToken);`
- The token can also be set after construction: `$ploi->setApiToken($token);`
- The SDK auto-configures a Guzzle client pointing at `https://ploi.io/api/` with JSON content headers.

### Fluent Resource Chaining

- Access resources using the fluent parent-child chain. Always start from the `$ploi` instance and drill down:
  - `$ploi->server($serverId)` to target a server
  - `$ploi->server($serverId)->sites($siteId)` to target a site on a server
  - `$ploi->server($serverId)->sites($siteId)->certificates()` to access certificates on a site
- Pass the resource ID when you first access the resource in the chain, not as a separate call.
- Singular and plural method names are interchangeable on the entry point: `$ploi->server()` and `$ploi->servers()` both return a `Server` resource.

### Resource Hierarchy

- **Server-level resources** (accessed from `$ploi->server($id)->`): `sites()`, `databases()`, `cronjobs()`, `daemons()`, `sshKeys()`, `services()`, `networkRules()`, `systemUsers()`, `opcache()`, `insights()`, `loadBalancer()`
- **Site-level resources** (accessed from `->sites($id)->`): `certificates()`, `repository()`, `queues()`, `deployment()`, `app()`, `environment()`, `alias()`, `redirects()`, `fastCgi()`, `authUser()`, `robots()`, `tenants()`, `monitors()`, `nginxConfiguration()`
- **Database-level resources** (accessed from `->databases($id)->`): `backups()`, `users()`
- **Top-level resources** (accessed from `$ploi->`): `project()`, `scripts()`, `statusPage()`, `user()`, `webserverTemplates()`, `fileBackup()`

### Fetching Data

- Use `->get()` to list all resources or fetch a single one by ID.
- `->get()` returns a `Ploi\Http\Response` object. Use `->getJson()` for a `stdClass`, `->getData()` for the `data` property, or `->toArray()` for the full structure.
- When calling `->get($id)`, the ID parameter is optional if you already passed it during chaining.

### Creating Resources

- Each resource has a `create()` method with named parameters matching the API. Always check the method signature for required vs. optional parameters.
- Pass options as method arguments, not as raw arrays (unless the method signature accepts one).

### Pagination

- Resources with `HasPagination` support `->page($pageNumber, $perPage)` and `->perPage($amount)`.
- Example: `$ploi->server($id)->sites()->page(2, 15);`

### Error Handling

- Wrap API calls in try/catch blocks. The SDK throws typed exceptions based on HTTP status codes:
  - `Ploi\Exceptions\Http\Unauthenticated` (401)
  - `Ploi\Exceptions\Http\NotFound` (404)
  - `Ploi\Exceptions\Http\NotAllowed` (405)
  - `Ploi\Exceptions\Http\NotValid` (422)
  - `Ploi\Exceptions\Http\TooManyAttempts` (429)
  - `Ploi\Exceptions\Http\InternalServerError` (500)
  - `Ploi\Exceptions\Http\PerformingMaintenance` (503)
- A `Ploi\Exceptions\Resource\RequiresId` is thrown when a resource method needs an ID but none was provided.

### Deployment

- Use the `deployment()` resource on a site: `$ploi->server($id)->sites($siteId)->deployment()->deploy();`
- Access and update deploy scripts: `->deployment()->deployScript()` and `->deployment()->updateDeployScript($script)`.
- Quick deploy toggle is on the repository resource: `->repository()->toggleQuickDeploy()`.

### API Call Options

- When making raw API calls or extending the SDK, pass body data as `['body' => json_encode([...])]` (Guzzle options format).
- Only `get`, `post`, `patch`, and `delete` HTTP methods are supported.

## Examples

### Initialize the client

```php
use Ploi\Ploi;

$ploi = new Ploi('your-api-token');
```

### List all servers with pagination

```php
$response = $ploi->servers()->page(1, 10);
$servers = $response->getData();
```

### Get a single server

```php
$server = $ploi->server(123)->get();
echo $server->getData()->name;
```

### Create a site on a server

```php
$response = $ploi->server(123)->sites()->create(
    domain: 'example.com',
    webDirectory: '/public',
    projectRoot: '/',
    systemUser: 'ploi'
);
```

### Install a repository and deploy

```php
$ploi->server(123)->sites(456)->repository()->install(
    provider: 'github',
    branch: 'main',
    name: 'owner/repo'
);

$ploi->server(123)->sites(456)->deployment()->deploy();
```

### Manage SSL certificates

```php
// List certificates
$certs = $ploi->server(123)->sites(456)->certificates()->get();

// Create a Let's Encrypt certificate
$ploi->server(123)->sites(456)->certificates()->create(
    certificate: 'example.com',
    type: 'letsencrypt'
);
```

### Database management

```php
// Create a database
$ploi->server(123)->databases()->create(
    name: 'my_app',
    user: 'my_user',
    password: 'secret'
);

// Set up automated backups
$ploi->server(123)->databases(789)->backups()->create(
    interval: 1440,
    type: 'to_server'
);
```

### Queue and worker management

```php
$ploi->server(123)->sites(456)->queues()->create(
    connection: 'redis',
    queue: 'default',
    maximumSeconds: 60,
    sleep: 30,
    processes: 3,
    maximumTries: 3
);
```

### Update environment variables

```php
$ploi->server(123)->sites(456)->environment()->update(
    content: "APP_ENV=production\nAPP_DEBUG=false\nAPP_KEY=base64:..."
);
```

### Error handling

```php
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\NotValid;
use Ploi\Exceptions\Http\Unauthenticated;

try {
    $server = $ploi->server(999)->get();
} catch (Unauthenticated $e) {
    // Invalid API token
} catch (NotFound $e) {
    // Server not found
} catch (NotValid $e) {
    // Validation error - check the response body for details
}
```

### Manage daemons

```php
// Create a daemon
$ploi->server(123)->daemons()->create(
    command: 'php artisan horizon',
    systemUser: 'ploi',
    processes: 1,
    directory: '/home/ploi/example.com'
);

// Restart a daemon
$ploi->server(123)->daemons(789)->restart();
```

### Manage cron jobs

```php
$ploi->server(123)->cronjobs()->create(
    command: 'php /home/ploi/example.com/artisan schedule:run',
    frequency: '* * * * *',
    user: 'ploi'
);
```

### Service management

```php
// Restart nginx
$ploi->server(123)->services('nginx')->restart();

// Restart MySQL
$ploi->server(123)->services('mysql')->restart();
```

## Anti-patterns

### Do not re-fetch the client for every call

```php
// Bad - creating multiple instances
$servers = (new Ploi($token))->servers()->get();
$sites = (new Ploi($token))->server(1)->sites()->get();

// Good - reuse the client
$ploi = new Ploi($token);
$servers = $ploi->servers()->get();
$sites = $ploi->server(1)->sites()->get();
```

### Do not manually build API URLs

```php
// Bad - constructing URLs by hand
$ploi->makeAPICall('servers/123/sites/456/certificates', 'get');

// Good - use the fluent chain
$ploi->server(123)->sites(456)->certificates()->get();
```

### Do not ignore typed exceptions

```php
// Bad - catching generic exceptions
try {
    $ploi->server(123)->get();
} catch (\Exception $e) {
    echo "Something went wrong";
}

// Good - catch specific exceptions for proper handling
try {
    $ploi->server(123)->get();
} catch (TooManyAttempts $e) {
    sleep(60); // Wait and retry for rate limiting
} catch (NotFound $e) {
    // Handle missing resource
} catch (Unauthenticated $e) {
    // Handle invalid token
}
```

### Do not pass IDs twice

```php
// Bad - redundant ID passing
$ploi->server(123)->sites(456)->certificates()->get(789);
// And then again:
$ploi->server(123)->sites(456)->certificates(789)->get(789);

// Good - pass the ID once, either in the chain or in the method
$ploi->server(123)->sites(456)->certificates(789)->get();
// or
$ploi->server(123)->sites(456)->certificates()->get(789);
```

### Do not access raw Guzzle responses when the SDK provides helpers

```php
// Bad - decoding manually
$response = $ploi->servers()->get();
$body = json_decode($response->getResponse()->getBody()->getContents());

// Good - use the Response helper methods
$response = $ploi->servers()->get();
$data = $response->getData();      // Parsed data property
$json = $response->getJson();      // Full parsed JSON
$array = $response->toArray();     // Array with json + response
```

## References

- [Ploi API Documentation](https://developers.ploi.io)
- [Ploi PHP SDK GitHub Repository](https://github.com/ploi/ploi-php-sdk)
- [Ploi.io](https://ploi.io)
