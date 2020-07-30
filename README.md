# Laravel Tarantool Session Driver

A Tarantool session driver for Laravel. For more information about sessions, check http://laravel.com/docs/session.

## Requirements

- PHP ^7.2
- Laravel ^5.5

## Installation

Via Composer

```bash
composer require aidynmakhataev/laravel-tarantool-session
```

Laravel uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

### Laravel without auto-discovery:

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php

```php
\AidynMakhataev\Tarantool\Session\SessionServiceProvider::class
```
### Change session driver

Change the session driver in `config/session.php` to tarantool:

    'driver' => 'tarantool',

## Configuration

You can publish the config file with the following command:

```bash
php artisan vendor:publish --tag="tarantool-session-config"
```

You need to provide following tarantool connection variables
```dotenv
TARANTOOL_SESSION_HOST=tcp://tarantool
TARANTOOL_SESSION_USER=user
TARANTOOL_SESSION_PASSWORD=password
TARANTOOL_SESSION_SPACE=sessions
```

## License

MIT. Please see the [license file](LICENSE) for more information.
