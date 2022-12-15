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

        // Is Superadmin Gate
        Gate::define('is-superadmin', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 1;
            }
        });

        // Is Verifier Gate
        Gate::define('is-verifier', function (User $user) {
            foreach ($user->role as $userRole) {
                return $userRole->level == 3;
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
