<?php

namespace Hilabs\OneAuth;

use Hilabs\OneAuth\OneAuth;
use Hilabs\OneAuth\OneAuthUserProvider;
use Hilabs\OneAuth\Contract\UserRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class OneAuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerHelpers();
    }

    /**
     * Register the helpers file.
     */
    public function registerHelpers()
    {
        require __DIR__.'/helpers.php';

        Auth::provider('one-auth', function($app, array $config) {
            $userRepository = App::make(UserRepository::class);
            return new OneAuthUserProvider($userRepository);
        });

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('one-auth.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton("one-auth", function() {
            return new OneAuth();
        });

        Event::listen('auth.logout', function() {
            App::make("one-auth")->logout();
        });
    }
}
