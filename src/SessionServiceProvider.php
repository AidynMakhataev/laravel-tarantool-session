<?php

declare(strict_types=1);

namespace AidynMakhataev\Tarantool\Session;

use AidynMakhataev\Tarantool\Session\Console\TransferSessionFromFileCommand;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Tarantool\Client\Client;

/**
 * Class SessionServiceProvider
 * @package AidynMakhataev\Tarantool\Session
 */
final class SessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tarantool-session.php', 'tarantool-session'
        );

        $this->commands([
            TransferSessionFromFileCommand::class
        ]);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tarantool-session.php' => $this->app->configPath('tarantool-session.php'),
            ], 'tarantool-session-config');
        }

        $this->app->singleton(TarantoolSessionHandler::class, static function (Application $app) {
            $options = $app['config']['tarantool-session'];

            $client = Client::fromOptions([
                'uri'       =>  $options['host'],
                'username'  =>  $options['user'],
                'password'  =>  $options['password'],
            ]);

            return new TarantoolSessionHandler($client, $options['space']);
        });

        Session::extend('tarantool', static function (Application $app) {
            return $app->make(TarantoolSessionHandler::class);
        });
    }
}
