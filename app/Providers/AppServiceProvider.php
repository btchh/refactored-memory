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
        //
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
