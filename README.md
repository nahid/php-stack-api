# php-stack-api

php-stack-api is a php SDK for StackExchange api version 2.2. Now you can easily access and hanlde all of StackApps Apis. Lets enjoy :)

## Requirements

- CURL
- PHP 5.5.9 +

## Installation

To install this package with your project, just run this command in your terminal from project root.

```shell
composer require nahid/php-stack-api
```

## Configurations

After installation complete successfully, you have to configure it correctly. This package is also **Laravel** compatible. 
### Laravel 

Open `config/app.php` and add this line end of the providers array

```
Nahid\StackApis\StackApiServiceProvider::class,
```

and then run this command in your terminal to publish config file

```
php artisan vendor: publish --provider="Nahid\StackApis\StackApiServiceProvider"
```
When you run this command `stackapi.php` config file will be copy `config` directory of your project. Now open `app/stackapi.php` and file the credentials with your StackApps application.

```php
return [
    'client_id' => 1234,
    'client_secret' => 'application-client-secret',
    'key' => 'application-key-value',
    'redirect_uri' => 'http://example.com/redirect-uri',
];
```
### Pure PHP Project

There are no special configuration for pure PHP project. You have to pass configuration data when the class is instantiated.


```php
require 'vendor/autoload.php';

use Nahid\StackApis\StackApi;

$config = [
    'client_id' => 1234,
    'client_secret' => 'application-client-secret',
    'key' => 'application-key-value',
    'redirect_uri' => 'http://example.com/redirect-uri',
];
$stackApi = new StackApi($config);
```

## Usage

Its has a lots of functionalities. When all of these are configured, you just use it like what you what.

At first you need to authenticate an user with your project. So make a authentication link.


```php
<a href="<?= get_stack_api_auth_url(); ?>">Authenticate</a>
```

Its make a authentication url for StackExchange and get `access_token` with your redirect_url. Now you are an authorized user and access all API from this package.

### Get Currently Authenticated Users Data

*API URI*: `/me`

#### For Laravel

```php
$me = StackApi::me()->get();
```

#### Pure PHP

```php
$stackApi = new StackApi($config);
$me = $stackApi->me()->get();
```

### Users Information By User ID

*API URI*: `/users/{ids}`

#### For Laravel

```php
$me = StackApi::users(1234)->get();
```

#### Pure PHP

```php
$stackApi = new StackApi($config);
$me = $stackApi->users(1234)->get();
```

Here all of these data are Objects. So you can easily handle it. 


## The Easy Way

This package is well formatted as like as RestAPI URI. Really you amazed to see it.

For example, you want to get data where URI is `/users/{ids}/comments` so the method will look like these

```php
$data = StackApi::users($id)->comments()->get();
```

Or, if the URI is `/users/{id}/network-activity` so the method will be 

```php
$data = StackApi::users($id)->networkActivity()->get();
```

> **Note:** 1. every URI you can call as PHP method chaining as per URI format.
> 2. If you have a hyphen (-) separated URI, it will transform to camel case(camelCase) when you called as a method
> 3. Every URI parameter goes to method parameter as per URI format. 

Here the final example, if you have a URI like `/badges/{ids}/recipients`

```php
$data = StackApi::badges($id)->recipients()->get();
```

## Available Methods

- `makeAuthUri()`
- `getAccessToken()`
- `info([$site[, $sort]])`
- `me()`
- `users($ids)`
- `user($id)`
- `get([$site[, $page[, $pageSize[, $sort[, $order[, $dateRange[, $minDate]]]]]]])`
- `isExpired()`
- `destroyAccessToken()`

For more about StackApps API you can read [documentation](https://api.stackexchange.com/docs)

Thank you :)



