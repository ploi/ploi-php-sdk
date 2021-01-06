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
When calling a resource, it will return an array containing decoded JSON as well as the original response from the Guzzle client.

```json
[
    "json" : {
        "id": 123,
        "name": "server-name",
        "ip_address": "XXX.XXX.XXX.XXX",
        "php_version": 7.1,
        "mysql_version": 5.7,
        "sites_count": 3,
        "status": "Server active",
        "created_at": "2018-01-01 08:00:00",
     },
     "response" : GuzzleHttp\Psr7\Response,
]
```

You can also only retrieve the JSON, use the `getJson()` method to only get the JSON back:

`$ploi->user()->get()->getJson()`

However, when you want to only get the data, use the `getData()` method:

`$ploi->user()->get()->getData()`

## Resources

Resources are what you call to access a feature or function. 

### Servers


```php
$ploi->server()->get();
```

```php
// Get specific server
$ploi->server(123)->get();
// or
$ploi->server()->get(123);

// Get server logs
$ploi->server(123)->logs()->getJson();
// or
$ploi->server()->logs(123)->getJson();
```

### Sites

```php
// Get all sites
$ploi->server(123)->sites()->get();

// Get specific site
$ploi->server(123)->sites(123)->get();
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
