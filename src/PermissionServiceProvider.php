<?php

namespace Anfragen\Permission;

use Anfragen\Permission\Commands\{CreatePermission, CreateRole, ResetCache};
use Anfragen\Permission\Middleware\{CheckPermission, CheckRole};
use Anfragen\Permission\Models\{ModelPermission, ModelRole, Permission, PermissionRole, Role};
use Anfragen\Permission\Observers\{ModelPermissionObserver, ModelRoleObserver, PermissionObserver, PermissionRoleObserver, RoleObserver};
use Illuminate\Foundation\Auth\User;
use Illuminate\Routing\Router;
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
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'anfragen');

        $this->publishesFiles();

        $this->configureGate();

        $this->registerCommands();

        $this->registerObservers();

        $this->registerMiddleware();
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
            if (Permission::getPermission($ability)) {
                return $user->hasPermissionTo($ability) || $user->hasPermissionByRoleTo($ability);
            }
        });
    }

    /**
     * Register the package's commands.
     */
    private function registerCommands(): void
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
     * Register the package's observers.
     */
    private function registerObservers(): void
    {
        Role::observe(RoleObserver::class);

        ModelRole::observe(ModelRoleObserver::class);

        Permission::observe(PermissionObserver::class);

        PermissionRole::observe(PermissionRoleObserver::class);

        ModelPermission::observe(ModelPermissionObserver::class);
    }

    /**
     * Register the package's middleware.
     */
    private function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('roles', CheckRole::class);
        $router->aliasMiddleware('permissions', CheckPermission::class);
    }

    /**
     * Return the migration name.
     */
    private function returnMigrationName(): string
    {
        return database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permissions_table.php');
    }
}
