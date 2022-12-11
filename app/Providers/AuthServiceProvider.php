<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
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

        // Gate CRUD Users --superadmin
        Gate::define('get-create-delete-users', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 1;
            }
        });

        // Gate Show,Update User By Id --superadmin/allAuthenticated
        Gate::define('show-update-users', function (User $user, $id) {
            foreach ($user->role as $userRole) {
                if ($userRole->level == 1 || $user->user_id == $id) {
                    return true;
                }
            }
        });

        // Gate CRUD Vehicle Usage --superadmin/allAuthenticated
        Gate::define('get-show-store-update-delete-vehicle_usages', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 1;
            }
        });
    }
}
