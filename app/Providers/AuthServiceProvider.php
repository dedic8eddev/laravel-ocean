<?php

namespace App\Providers;

use App\Http\Controllers\Auth\JwtGuard;
use App\Services\JwtService\JwtService;
use App\Services\JwtService\JwtServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->app->singleton(JwtService::class, function () {
            return new JwtServiceImpl(config('auth.jwt.key'));
        });

        Auth::extend('jwt', function($app, $name, array $config) {
            /** @var \Illuminate\Foundation\Application $app */
            return new JwtGuard(
                $app->make(Request::class),
                Auth::createUserProvider($config['provider']),
                $app->make(JwtService::class)
            );
        });
    }
}
