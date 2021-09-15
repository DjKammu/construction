<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

           $this->registerPostPolicies();
    }

    public function registerPostPolicies()
    {
        Gate::define(Role::VIEW, function ($user) {
            return $user->hasAccess(Role::VIEW) || $user->isAdmin();
        });
        Gate::define(Role::ADD, function ($user) {
            return $user->hasAccess(Role::ADD) || $user->isAdmin();
        });
        Gate::define(Role::EDIT, function ($user) {
            return $user->hasAccess(Role::EDIT) || $user->isAdmin();
        });
        Gate::define(Role::UPDATE, function ($user) {
            return $user->hasAccess(Role::UPDATE) || $user->isAdmin();
        });
        Gate::define(Role::DELETE, function ($user) {
            return $user->hasAccess(Role::DELETE) || $user->isAdmin();
        });
        Gate::define(Role::ADD_USERS, function ($user) {
            return $user->hasAccess(Role::ADD_USERS) || $user->isAdmin();
        });
    }
}
