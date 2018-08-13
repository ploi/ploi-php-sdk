## NOT READY FOR PRODUCTION YET

# Ploi PHP SDK 🚀

The future is now - so stop the hassle, you’re running behind. Quick and easy site deployment with Ploi. Awesome features for awesome developers. Check it out at www.ploi.io

This SDK is ment for PHP applications to be able to communicate with our API.
You can find our documentation at https://ploi.io/docs/api/index.html

## Installation

```
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

### Servers

Get all servers
```php
$ploi->server()->get();
```

Get a specific server
```php
$ploi->server(123)->get();
// or
$ploi->server()->get(123);
```

Get a servers deployment logs

```php
$ploi->server(123)->logs();
// or
$ploi->server()->logs(123);
```


