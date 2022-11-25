<?php

namespace Anfragen\Permission;

use Anfragen\Permission\Commands\{CreatePermission, CreateRole, ResetCache};
use Anfragen\Permission\Models\Permission;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/permissions.php', 'permissions');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'permissions');

        $this->publishesFiles();

        $this->registerCommands();

        $this->configureGate();
    }

    /**
     * Publishes files.
     */
    private function publishesFiles(): void
    {
        $this->publishes([
            __DIR__ . '/../config/permissions.php' => config_path('permissions.php'),
        ], 'permissions-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_permissions_table.php' => $this->returnMigrationName(),
        ], 'permissions-migrations');

        $this->publishes([
            __DIR__ . '/../lang' => lang_path(),
        ], 'permissions-lang');
    }

    /**
     * Configure the gate to use the permission model.
     */
    private function configureGate(): void
    {
        Gate::before(function (User $user, $ability) {
            if (method_exists($user, 'hasPermissionTo')) {
                return Permission::getPermission($ability) ? $user->hasPermissionTo($ability) : true;
            }
        });
    }

    /**
     * Register the package's commands.
     */
    public function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreatePermission::class,
                CreateRole::class,
                ResetCache::class,
            ]);
        }
    }

    /**
     * Return the migration name.
     */
    private function returnMigrationName(): string
    {
        return database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permissions_table.php');
    }
}
