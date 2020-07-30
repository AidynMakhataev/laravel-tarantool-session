<?php

declare(strict_types=1);

namespace AidynMakhataev\Tarantool\Session;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

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
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tarantool-session.php' => $this->app->configPath('tarantool-session.php'),
            ]);
        }


        Session::extend('tarantool', static function ($app) {
            $options = $app['config']['tarantool-session'];

            return new TarantoolSessionHandler(
                $options['host'],
                $options['user'],
                $options['password'],
                $options['space']
            );
        });
    }
}