<?php

namespace App\Providers;

use App\Database\Connectors\SqlServerConnector;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('db.connector.sqlsrv', function () {
            return new SqlServerConnector();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
