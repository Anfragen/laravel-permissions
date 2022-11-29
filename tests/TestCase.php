<?php

namespace Anfragen\Permission\Tests;

use Anfragen\Permission\Models\{Permission, Role};
use Anfragen\Permission\PermissionServiceProvider;
use Anfragen\Permission\Tests\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Define Package Service Providers.
     */
    protected function getPackageProviders($app)
    {
        return [
            PermissionServiceProvider::class,
        ];
    }

    /**
     * Define Environment Setup.
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.prefix', 'anfragen::tests::');

        $app['config']->set('cache.default', env('CACHE_DRIVER', 'redis'));
        $app['config']->set('session.driver', env('SESSION_DRIVER', 'redis'));

        $app['config']->set('database.default', env('DB_CONNECTION', 'mysql'));
        $app['config']->set('database.connections.mysql.host', env('DB_HOST', 'mysql'));
        $app['config']->set('database.connections.mysql.port', env('DB_PORT', '3306'));
        $app['config']->set('database.connections.mysql.database', env('DB_DATABASE', 'anfragen'));
        $app['config']->set('database.connections.mysql.username', env('DB_USERNAME', 'sail'));
        $app['config']->set('database.connections.mysql.password', env('DB_PASSWORD', 'password'));

        $app['config']->set('database.redis.cache.host', env('REDIS_HOST', 'redis'));
        $app['config']->set('database.redis.cache.password', env('REDIS_PASSWORD', null));
        $app['config']->set('database.redis.cache.port', env('REDIS_PORT', '6379'));
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->runFactories();
    }

    /**
     * Create Values using the Factories
     */
    protected function runFactories()
    {
        $users = User::factory()->count(3)->create();

        $roles = Role::factory()->count(4)->create();

        $permissions = Permission::factory()->count(50)->create();

        $users->each(fn ($user) => $user->assignRoleTo($roles->random(1)->first()->id));

        $users->each(fn ($user) => $user->syncPermissions($permissions->random(3)->pluck('id')));

        $roles->each(fn ($role) => $role->syncPermissions($permissions->random(10)->pluck('id')));
    }
}
