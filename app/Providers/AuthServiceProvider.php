<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;


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

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Is Superadmin Gate
        Gate::define('is-superadmin', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 1;
            }
        });

        // Is Validator Gate
        Gate::define('is-validator', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 2;
            }
        });

        // Is Verifier Gate
        Gate::define('is-verifier', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 3;
            }
        });

        // Is Driver Gate
        Gate::define('is-driver', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 4;
            }
        });

        // Is Superadmin Or Current User Gate
        Gate::define('is-superadmin-or-currentuser', function (User $user, $id) {
            foreach ($user->role as $userRole) {
                if ($userRole->level == 1 || $user->user_id == $id) {
                    return true;
                }
            }
        });
    }
}
