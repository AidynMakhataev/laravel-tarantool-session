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

license. Please see the [license file](license.md) for more information.
