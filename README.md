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
// List servers
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

// Paginate servers
$ploi->servers()->perPage($amountPerPage)->page($pageNumber);
// or
$ploi->servers()->page($pageNumber, $amountPerPage);

// Get server
$ploi->servers(123)->get();

// Delete server
$ploi->servers(123)->delete();

// Get server logs
$ploi->servers(123)->logs();

// Restart server
$ploi->servers(123)->restart();

// Get server monitoring
$ploi->servers(123)->monitoring();

// Get PHP versions installed on server
$ploi->servers(123)->phpVersions();

// Enable opcache
$ploi->servers(123)->opcache()->enable();

// Disable opcache
$ploi->servers(123)->opcache()->disable();

// Refresh opcache
$ploi->servers(123)->opcache()->refresh();
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
    $systemUserPassword = null,
    $webserverTemplate = null,
    $projectType = null
);

// List sites
$ploi->servers(123)->sites()->get();

// Paginate sites
$ploi->servers(123)->sites()->perPage(15)->page(1);

// Get site
$ploi->servers(123)->sites(123)->get();

// Delete site
$ploi->servers(123)->sites(123)->delete();

// Update site
$ploi->servers(123)->sites(123)->update($rootDomain);

// Get site logs
$ploi->servers(123)->sites(123)->logs();

// Set PHP version for site to use
$ploi->servers(123)->sites(123)->phpVersion($phpVersion);

// Enable test domain on site
$ploi->servers(123)->sites(123)->enableTestDomain();
// Disable test domain on site
$ploi->servers(123)->sites(123)->disableTestDomain();
// Get test domain details for site
$ploi->servers(123)->sites(123)->testDomain();

// Suspend site
$ploi->servers(123)->sites(123)->suspend($id = null, $reason = null);
// Resume site
$ploi->servers(123)->sites(123)->resume();

// Get Laravel Horizon statistics
$ploi->servers(123)->sites(123)->horizonStatistics($type);
```

### Databases

Available methods for databases:
```php
// Create database
$ploi->servers(123)->databases()->create(
    $databaseName,
    $databaseUser,
    $databaseUserPassword,
    $description = null,
    $siteId = null
);

// List databases
$ploi->servers(123)->databases()->get();

// Paginate databases
$ploi->servers(123)->databases()->perPage($amountPerPage)->page($pageNumber);

// Get database
$ploi->servers(123)->databases(123)->get();

// Delete database
$ploi->servers(123)->databases(123)->delete();

// Acknowledge database
$ploi->servers(123)->databases()->acknowledge($databaseName);

// Forget database
$ploi->servers(123)->databases(123)->forget();

// Duplicate database
$ploi->servers(123)->databases(123)->duplicate($name, $user = null, $password = null);
```

### Database Backups

Available methods for database backups:
```php
// Create database backup
$ploi->servers(123)->databases(123)->backups()->create(
    $interval,
    $type,
    $table_exclusions = null,
    $locations = null,
    $path = null
);

// List database backups
$ploi->servers(123)->databases(123)->backups()->get();

// Paginate database backups
$ploi->servers(123)->databases(123)->backups()->perPage($amountPerPage)->page($pageNumber);

// Get database backup
$ploi->servers(123)->databases(123)->backups(123)->get();

// Delete database backup
$ploi->servers(123)->databases(123)->backups(123)->delete();

// Toggle database backup
$ploi->servers(123)->databases(123)->backups(123)->toggle();
```

### File Backups

Available methods for file backups:
```php
// List site file backups
$ploi->fileBackups()->get();

// Paginate file backups
$ploi->fileBackups()->perPage($amountPerPage)->page($pageNumber);

// Get specific file backup
$ploi->fileBackups(123)->get();

// Create file backup
$ploi->fileBackups()->create(
    $backup_configuration,
    $server,
    $sites,
    $interval,
    $path,
    $locations = null,
    $keep_backup_amount = null,
    $custom_name = null,
    $password = null
);

// Run file backup
$ploi->fileBackups(123)->run();

// Delete file backup
$ploi->fileBackups(123)->delete();
```

### Database Users

Available methods for database users:
```php
// Create database user
$ploi->servers(123)->databases(123)->users()->create(
    $user,
    $password,
);

// List database users
$ploi->servers(123)->databases(123)->users()->get();

// Paginate database users
$ploi->servers(123)->databases(123)->users()->perPage($amountPerPage)->page($pageNumber);

// Get database user
$ploi->servers(123)->databases(123)->users(123)->get();

// Delete database user
$ploi->servers(123)->databases(123)->users(123)->delete();
```

### Cronjobs

Available methods for cronjobs:
```php
// Create cronjob
$ploi->servers(123)->cronjobs()->create(
    $command,
    $frequency,
    $user = 'ploi'
);

// List cronjobs
$ploi->servers(123)->cronjobs()->get();

// Paginate cronjobs
$ploi->servers(123)->cronjobs()->perPage($amountPerPage)->page($pageNumber);

// Get cronjob
$ploi->servers(123)->cronjobs(123)->get();

// Delete cronjob
$ploi->servers(123)->cronjobs(123)->delete();
```

### Network Rules

Available methods for network rules:
```php
// Create network rule
$ploi->servers(123)->networkRules()->create(
    $name,
    $port,
    $type = 'tcp',
    $fromIpAddress = null,
    $ruleType = 'allow'
);

// List network rules
$ploi->servers(123)->networkRules()->get();

// Paginate network rules
$ploi->servers(123)->networkRules()->perPage($amountPerPage)->page($pageNumber);

// Get network rule
$ploi->servers(123)->networkRules(123)->get();

// Delete network rule
$ploi->servers(123)->networkRules(123)->delete();
```

### Queues

Available methods for queues:
```php
// Create queue
$ploi->servers(123)->sites(123)->queues()->create(
    $connection = 'database',
    $queue = 'default',
    $maximumSeconds = 60,
    $sleep = 30,
    $processes = 1,
    $maximumTries = 1
);

// List queues
$ploi->servers(123)->sites(123)->queues()->get();

// Paginate queues
$ploi->servers(123)->sites(123)->queues()->perPage($amountPerPage)->page($pageNumber);

// Get queue
$ploi->servers(123)->sites(123)->queues(123)->get();

// Delete queue
$ploi->servers(123)->sites(123)->queues(123)->delete();

// Pause queue
$ploi->servers(123)->sites(123)->queues(123)->pause();

// Restart queue
$ploi->servers(123)->sites(123)->queues(123)->restart();
```

### Certificates

Available methods for certificates:
```php
// Create certificate
$ploi->servers(123)->sites(123)->certificates()->create(
    $certificate,
    $type = 'letsencrypt'
);

// List certificates
$ploi->servers(123)->sites(123)->certificates()->get();

// Paginate certificates
$ploi->servers(123)->sites(123)->certificates()->perPage($amountPerPage)->page($pageNumber);

// Get certificate
$ploi->servers(123)->sites(123)->certificates(123)->get();

// Delete certificate
$ploi->servers(123)->sites(123)->certificates(123)->delete();
```

### NGINX Configuration

Available methods for NGINX configuration:
```php
// Get NGINX configuration
$ploi->servers(123)->sites(123)->nginxConfiguration()->get();

// Update NGINX configuration
$ploi->servers(123)->sites(123)->nginxConfiguration()->update($configuration);
```

### Load Balancers

Available methods for load balancers
```php
// Request certificate
$ploi->servers(123)->loadBalancer()->requestCertificate($domain);

// Revoke certificate
$ploi->servers(123)->loadBalancer()->revokeCertificate($domain);

```

### Auth Users

Available methods for auth users:
```php
// Create auth user
$ploi->servers(123)->sites(123)->authUser()->create(
    $name,
    $password
);

// List auth users
$ploi->servers(123)->sites(123)->authUser()->get();

// Paginate auth users
$ploi->servers(123)->sites(123)->authUser()->perPage($amountPerPage)->page($pageNumber);

// Get auth user
$ploi->servers(123)->sites(123)->authUser(123)->get();

// Delete auth user
$ploi->servers(123)->sites(123)->authUser(123)->delete();
```

### Deployments

Available methods for deployments
```php
// Get default deploy script
$ploi->servers(123)->sites(123)->deployment()->deployScript();

// Update default deploy script
$ploi->servers(123)->sites(123)->deployment()->updateDeployScript($script = '');

// Deploy a site
$ploi->servers(123)->sites(123)->deployment()->deploy();

// Deploy a staging site to production
$ploi->servers(123)->sites(123)->deployment()->deployToProduction();
```

### Environments

Available methods for environments
```php
// Get .env for site
$ploi->servers(123)->sites(123)->environment()->get();

// Update .env for site
$ploi->servers(123)->sites(123)->environment()->update($content);
```

### Repositories

Available methods for repositories:
```php
// Install repository
$ploi->servers(123)->sites(123)->repository()->install(
    $provider,
    $branch,
    $name
);

// Get repository
$ploi->servers(123)->sites(123)->repository()->get();

// Delete repository
$ploi->servers(123)->sites(123)->repository()->delete();

// Toggle quick deploy on repository
$ploi->servers(123)->sites(123)->repository()->toggleQuickDeploy();
```

### Redirects

```php
// Create redirect
$ploi->servers(123)->sites(123)->redirects()->create(
    $redirectFrom,
    $redirectTo,
    $type = 'redirect'
);

// List redirects
$ploi->servers(123)->sites(123)->redirects()->get();

// Paginate redirects
$ploi->servers(123)->sites(123)->redirects()->perPage($amountPerPage)->page($pageNumber);

// Get redirect
$ploi->servers(123)->sites(123)->redirects(123)->get();

// Delete redirect
$ploi->servers(123)->sites(123)->redirects(123)->delete();
```

### Aliases

```php
// Create aliases
$ploi->servers(123)->sites(123)->aliases()->create($aliases);

// List aliases
$ploi->servers(123)->sites(123)->aliases()->get();

// Delete alias
$ploi->servers(123)->sites(123)->aliases()->delete($alias);
```

### FastCGI Cache

```php
// Enable FastCGI cache
$ploi->servers(123)->sites(123)->fastCgi()->enable();

// Disable FastCGI cache
$ploi->servers(123)->sites(123)->fastCgi()->disable();

// Flush FastCGI cache
$ploi->servers(123)->sites(123)->fastCgi()->flush();
```

### Tenants

```php
// Create tenant
$ploi->servers(123)->sites(123)->tenants()->create($tenants);

// List tenants
$ploi->servers(123)->sites(123)->tenants()->get();

// Delete tenant
$ploi->servers(123)->sites(123)->tenants()->delete($tenant);

// Request tenant certificate
$ploi->servers(123)->sites(123)->tenants()->requestCertificate($tenant);

// Revoke tenant certificate
$ploi->servers(123)->sites(123)->tenants()->revokeCertificate($tenant);
```

### Robot Access

```php
// Allow robot access
$ploi->servers(123)->sites(123)->robots()->allow();

// Block robot access
$ploi->servers(123)->sites(123)->robots()->block();
```

### Monitors

```php
// List monitors
$ploi->servers(123)->sites(123)->monitors()->get();

// Paginate monitors
$ploi->servers(123)->sites(123)->monitors()->perPage($amountPerPage)->page($pageNumber);

// Get specific monitor
$ploi->servers(123)->sites(123)->monitors(123)->get();

// Get uptime responses
$ploi->servers(123)->sites(123)->monitors(123)->uptimeResponses();
```

### Scripts

Available methods for scripts:
```php
// Create script
$ploi->scripts()->create($label, $user, $content);

// List scripts
$ploi->scripts()->get();

// Paginate scripts
$ploi->scripts()->perPage($amountPerPage)->page($pageNumber);

// Get script
$ploi->scripts(123)->get();

// Delete script
$ploi->scripts(123)->delete();

// Run script
$ploi->scripts(123)->run($id = null, $serverIds = []);
```

### Daemons

Available methods for daemons:
```php
// Create daemon
$ploi->servers(123)->daemons()->create(
    $command,
    $systemUser,
    $processes,
    $directory = null
);

// List daemons
$ploi->servers(123)->daemons()->get();

// Paginate daemons
$ploi->servers(123)->daemons()->perPage($amountPerPage)->page($pageNumber);

// Get daemon
$ploi->servers(123)->daemons(123)->get();

// Delete daemon
$ploi->servers(123)->daemons(123)->delete();

// Pause daemon
$ploi->servers(123)->daemons(123)->pause();

// Restart daemon
$ploi->servers(123)->daemons(123)->restart();
```

### Services
```php
// Restart service
$ploi->servers(123)->services($name)->restart();
```

### System Users

Available methods for system users:
```php
// Create system user
$ploi->servers(123)->systemUsers()->create(
    $name,
    $sudo = false
);

// List system users
$ploi->servers(123)->systemUsers()->get();

// Paginate system users
$ploi->servers(123)->systemUsers()->perPage($amountPerPage)->page($pageNumber);

// Get system users
$ploi->servers(123)->systemUsers(123)->get();

// Delete system user
$ploi->servers(123)->systemUsers(123)->delete();
```

### SSH Keys

Available methods for SSH keys:
```php
// Create SSH key
$ploi->servers(123)->sshKeys()->create(
    $name = 'ssh key name',
    $key = 'ssh key here',
    $systemUser = 'ploi'
);

// List SSH keys
$ploi->servers(123)->sshKeys()->get();

// Paginate SSH keys
$ploi->servers(123)->sshKeys()->perPage($amountPerPage)->page($pageNumber);

// Get SSH key
$ploi->servers(123)->sshKeys(123)->get();

// Delete SSH key
$ploi->servers(123)->sshKeys(123)->delete();
```

### Insights
```php
// List insights
$ploi->servers(123)->insights()->get();

// Paginate insights
$ploi->servers(123)->insights()->perPage($amountPerPage)->page($pageNumber);

// Get insight
$ploi->servers(123)->insights(123)->get();

// Get insight detail
$ploi->servers(123)->insights(123)->detail();

// Automatically fix insight
$ploi->servers(123)->insights(123)->automaticallyFix();

// Ignore insight
$ploi->servers(123)->insights(123)->ignore();

// Delete insight
$ploi->servers(123)->insights(123)->delete();
```

### User

Available methods for user:
```php
// Get own user information
$ploi->user()->get();

// List server providers
$ploi->user()->serverProviders();

// Get server providers
$ploi->user()->serverProviders($providerId);
```

### Status Pages

Available methods for status pages:
```php
// List status pages
$ploi->statusPage()->get();

// Paginate status pages
$ploi->statusPage()->perPage($amountPerPage)->page($pageNumber);

// Get status page
$ploi->statusPage(123)->get();
```

### Status Page Incidents

Available methods for status page incidents:
```php
// Create incident
$ploi->statusPage(123)->incident()->create(
    $title,
    $description,
    $severity
);

// List incidents
$ploi->statusPage(123)->incident()->get();

// Paginate incidents
$ploi->statusPage(123)->incident()->perPage($amountPerPage)->page($pageNumber);

// Delete incident
$ploi->statusPage(123)->incident(123)->delete();
```

### Webserver Templates

Available methods for webserver templates:
```php
// List webserver templates
$ploi->webserverTemplates()->get();

// Paginate webserver templates
$ploi->webserverTemplates()->perPage($amountPerPage)->page($pageNumber);

// Get webserver template
$ploi->webserverTemplates(123)->get();
```
