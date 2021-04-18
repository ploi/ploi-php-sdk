# Ploi PHP SDK :rocket:

The future is now - so stop the hassle, you’re running behind. Quick and easy site deployment with Ploi. Awesome features for awesome developers. Check it out at https://ploi.io

This SDK is meant for PHP applications to be able to communicate with our API.
You can find our documentation at https://developers.ploi.io

## Installation

```bash
composer require ploi/ploi-php-sdk
```

## Usage

First you need to call a new Ploi instance

```php
$ploi = new \Ploi\Ploi($apiToken);
// or
$ploi = new \Ploi\Ploi();
$ploi->setApiToken($token);
```

### Responses
When calling a resource, it will return a `Ploi\Http\Response` object containing decoded JSON as well as the original response from the Guzzle client.

You can also only retrieve the JSON, use the `getJson()` method to only get the JSON back:

`$ploi->user()->get()->getJson()`

However, when you want to only get the data, use the `getData()` method:

`$ploi->user()->get()->getData()`

### Resources

Resources are what you call to access a feature or function.

You can get all the resources or get a specific one by its ID, for example with servers:
```php
// Get all servers
$ploi->servers()->get();

// Get a specific server with ID 123
$ploi->servers(123)->get();
// or
$ploi->servers()->get(123);
```

Some actions will require the resource's ID to be set before they can be used:
```php
// Throws Ploi\Exceptions\Resource\RequiresId
$ploi->servers()->delete();

// Will attempt to delete server by ID
$ploi->servers()->delete(123);
// or
$ploi->servers(123)->delete();
```

### Servers

You create a new server by:
```php
$ploi->servers()->create(
    $serverName,
    $providerId,
    $region,
    $plan,
    $options = []
);
```

Or you can create a custom server with a provider not set up in Ploi
```php
$ploi->servers()->createCustom($ip, $options);
```
After running this request, you will have to add the public key of the Ploi worker to your server.
This is included in the response with a 1-line command within the `ssh_command` key.
Once this is done, you can trigger the URL from the response with the `start_installation_url` key or by passing in the server ID.

```php
$ploi->servers()->startInstallation($installationUrl);
// or
$ploi->servers(123)->startInstallation();
```

Other methods for servers:
```php
// Get server list
$ploi->servers()->get();

// Get specific server
$ploi->servers(123)->get();

// Delete server
$ploi->servers(123)->delete();

// Get server logs
$ploi->servers(123)->logs();

// Get PHP versions installed on server
$ploi->servers(123)->phpVersions();

// Enable opcache
$ploi->servers(123)->enableOpcache();
// Disable opcache
$ploi->servers(123)->disableOpcache();
// Refresh opcache
$ploi->servers(123)->refreshOpcache();
```

### Sites

Available methods for sites:
```php
//Create site
$ploi->servers(123)->sites()->create(
    $domain,
    $webDirectory = '/public',
    $projectDirectory = '/',
    $systemUser = 'ploi',
    $systemUserPassword = null
);

// Get all sites
$ploi->servers(123)->sites()->get();

// Get specific site
$ploi->servers(123)->sites(123)->get();

// Delete site
$ploi->servers(123)->sites(123)->delete();

// Get site logs
$ploi->servers(123)->sites(123)->logs();

// Set PHP version for site to use
$ploi->servers(123)->sites(123)->phpVersion($phpVersion);

// Enable test domain on site
$ploi->servers(123)->sites(123)->enableTestDomain();
// Disable test domain on site
$ploi->servers(123)->sites(123)->enableTestDomain();
// Get test domain details for site
$ploi->servers(123)->sites(123)->testDomain();
```

### Databases

//

### Cronjobs

//

### Network Rules

//

### Queues

//

### Daemons

//

### System Users

//
