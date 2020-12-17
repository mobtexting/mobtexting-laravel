<?php

namespace NotificationChannels\Mobtexting;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Mobtexting\Client as MobtextingClient;

class MobtextingProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(MobtextingChannel::class)
            ->needs(Mobtexting::class)
            ->give(function () {
                return new Mobtexting(
                    $this->app->make(MobtextingClient::class),
                    $this->app->make(MobtextingConfig::class)
                );
            });

        $this->app->bind(MobtextingClient::class, function () {
            $config = $this->app['config']['services.mobtexting'];
            $username = Arr::get($config, 'username');
            if (!empty($username)) {
                $password = Arr::get($config, 'password');

                return new MobtextingClient($username, $password);
            } else {
                $token = Arr::get($config, 'token');

                return new MobtextingClient($token);
            }
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(MobtextingConfig::class, function () {
            return new MobtextingConfig($this->app['config']['services.mobtexting']);
        });
    }
}
