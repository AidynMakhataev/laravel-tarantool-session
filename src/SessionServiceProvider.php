<?php

declare(strict_types=1);

namespace AidynMakhataev\Tarantool\Session;

use AidynMakhataev\Tarantool\Session\Console\TransferSessionFromFileCommand;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Tarantool\Client\Client;

/**
 * Class SessionServiceProvider.
 */
final class SessionServiceProvider extends ServiceProvider
{
    public const DRIVER_NAME =  'tarantool';

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tarantool-session.php', 'tarantool-session'
        );

        $this->commands([
            TransferSessionFromFileCommand::class,
        ]);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tarantool-session.php' => $this->app->configPath('tarantool-session.php'),
            ], 'tarantool-session-config');
        }

        if ($this->app['config']['session']['driver'] === self::DRIVER_NAME) {
            $this->app->singleton(TarantoolSessionHandler::class, static function (Application $app) {
                $options = $app['config']['tarantool-session'];

                $client = Client::fromOptions([
                    'uri'       =>  $options['host'],
                    'username'  =>  $options['user'],
                    'password'  =>  $options['password'],
                ]);

                return new TarantoolSessionHandler($client, $options['space']);
            });

            Session::extend(self::DRIVER_NAME, static function (Application $app) {
                return $app->make(TarantoolSessionHandler::class);
            });
        }
    }
}
