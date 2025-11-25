<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repository bindings
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AdminRepositoryInterface::class,
            \App\Repositories\AdminRepository::class
        );

        // Register authentication services
        $this->app->singleton(\App\Services\Auth\AdminAuthService::class);
        $this->app->singleton(\App\Services\Auth\UserAuthService::class);
        $this->app->singleton(\App\Services\Auth\OtpService::class);

        // Register user management services
        $this->app->singleton(\App\Services\User\UserManagementService::class);
        $this->app->singleton(\App\Services\User\UserProfileService::class);

        // Register admin management services
        $this->app->singleton(\App\Services\Admin\AdminManagementService::class);
        $this->app->singleton(\App\Services\Admin\AdminProfileService::class);

        // Deprecated services - kept for backward compatibility, will be removed in future
        // These services have been replaced by feature-specific services above
        // DO NOT use these in new code
        // @deprecated Use Auth\AdminAuthService, Auth\UserAuthService instead
        // $this->app->singleton(\App\Services\AuthService::class);
        // @deprecated Use User\UserManagementService, User\UserProfileService instead
        // $this->app->singleton(\App\Services\UserService::class);
        // @deprecated Use Admin\AdminManagementService, Admin\AdminProfileService instead
        // $this->app->singleton(\App\Services\AdminService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Transaction::observe(\App\Observers\TransactionObserver::class);

        // Enable query logging in development to identify N+1 queries and performance issues
        // Logs are written to storage/logs/laravel.log
        // To monitor queries: tail -f storage/logs/laravel.log | grep "Query executed"
        if (config('app.debug')) {
            \DB::listen(function ($query) {
                \Log::channel('daily')->debug('Query executed', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms'
                ]);
            });
        }
    }
}
