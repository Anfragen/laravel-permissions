<?php

use Anfragen\Permission\Models\{ModelRole, Permission, Role};
use Anfragen\Permission\Tests\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->model = User::factory()->create();
});

/*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
*/

test('check if the roles are cached correctly', function () {
    $roles      = Role::all();
    $rolesCache = Role::getAllFromCache();

    $this->assertEquals($roles, $rolesCache);
    expect($roles->count())->toBe($rolesCache->count());

    $random = Role::inRandomOrder()->first();

    $this->assertEquals($random->toArray(), Role::getRole($random->id)->toArray());
    $this->assertEquals($random->toArray(), Role::getRole($random->uuid)->toArray());
    $this->assertEquals($random->toArray(), Role::getRole($random->slug)->toArray());
    $this->assertEquals($random->toArray(), Role::getRole($random->name)->toArray());
});

test('return model roles correctly and in this case empty', function () {
    $role = Role::getAllFromCache()->shuffle()->first();

    $modelRoles = $this->model->getRoles();

    expect($modelRoles)->toBeEmpty();
    expect($this->model->hasRoleTo($role->id))->toBeFalse();
    expect($this->model->hasRoleTo($role->uuid))->toBeFalse();
    expect($this->model->hasRoleTo($role->slug))->toBeFalse();
    expect($this->model->hasRoleTo($role->name))->toBeFalse();

    $this->assertDatabaseMissing('model_role', [
        'model_type' => User::class,
        'model_id'   => $this->model->id,
    ]);

    $roles = collect()->range(1, 30);

    $roles->each(
        fn () => expect($this->model->hasRoleTo(Str::random(8)))->toBeFalse()
    );
});

test('add a role for the user successfully', function () {
    $role = Role::getAllFromCache()->shuffle()->first();

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    $modelRoles = $this->model->getRoles();

    expect($modelRoles->count())->toBe(1);
    expect($this->model->hasRoleTo($role->id))->toBeTrue();
    expect($this->model->hasRoleTo($role->uuid))->toBeTrue();
    expect($this->model->hasRoleTo($role->slug))->toBeTrue();
    expect($this->model->hasRoleTo($role->name))->toBeTrue();

    $this->assertDatabaseHas('model_role', [
        'model_type' => User::class,
        'model_id'   => $this->model->id,
        'role_id'    => $role->id,
    ]);
});

test('remove model roles as requested', function () {
    $role = Role::getAllFromCache()->shuffle()->first();

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    expect($this->model->hasRoleTo($role->id))->toBeTrue();
    expect($this->model->hasRoleTo($role->uuid))->toBeTrue();
    expect($this->model->hasRoleTo($role->slug))->toBeTrue();
    expect($this->model->hasRoleTo($role->name))->toBeTrue();

    $this->assertDatabaseHas('model_role', [
        'model_type' => User::class,
        'model_id'   => $this->model->id,
        'role_id'    => $role->id,
    ]);

    $this->model->revokeRoleTo($role->id);
    $this->model->revokeRoleTo($role->uuid);
    $this->model->revokeRoleTo($role->slug);
    $this->model->revokeRoleTo($role->name);

    $modelRoles = $this->model->getRoles();

    expect($modelRoles)->toBeEmpty();
    expect($this->model->hasRoleTo($role->id))->toBeFalse();
    expect($this->model->hasRoleTo($role->uuid))->toBeFalse();
    expect($this->model->hasRoleTo($role->slug))->toBeFalse();
    expect($this->model->hasRoleTo($role->name))->toBeFalse();

    $this->assertDatabaseMissing('model_role', [
        'model_type' => User::class,
        'model_id'   => $this->model->id,
        'role_id'    => $role->id,
    ]);
});

test('sync roles for the user', function () {
    $roles = Role::getAllFromCache();

    $this->model->syncRoles($roles->pluck('id')->toArray());
    $this->model->syncRoles($roles->pluck('uuid')->toArray());
    $this->model->syncRoles($roles->pluck('slug')->toArray());
    $this->model->syncRoles($roles->pluck('name')->toArray());

    $modelRoles = $this->model->getRoles();

    $this->assertEquals($modelRoles->toArray(), $roles->toArray());
    expect($modelRoles->count())->toBe($roles->count());

    $roles->each(function (Role $role) {
        expect($this->model->hasRoleTo($role->id))->toBeTrue();
        expect($this->model->hasRoleTo($role->uuid))->toBeTrue();
        expect($this->model->hasRoleTo($role->slug))->toBeTrue();
        expect($this->model->hasRoleTo($role->name))->toBeTrue();

        $this->assertDatabaseHas('model_role', [
            'model_type' => User::class,
            'model_id'   => $this->model->id,
            'role_id'    => $role->id,
        ]);
    });

    $this->model->syncRoles([]);

    $this->assertDatabaseMissing('model_role', [
        'model_type' => User::class,
        'model_id'   => $this->model->id,
    ]);
});

test('validate if the user has at least one role assigned', function () {
    $roles = Role::getAllFromCache();

    $role = $roles->shuffle()->first();

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    expect($this->model->hasAnyRole($roles->pluck('id')->toArray()))->toBeTrue();
    expect($this->model->hasAnyRole($roles->pluck('uuid')->toArray()))->toBeTrue();
    expect($this->model->hasAnyRole($roles->pluck('slug')->toArray()))->toBeTrue();
    expect($this->model->hasAnyRole($roles->pluck('name')->toArray()))->toBeTrue();

    $this->model->revokeRoleTo($role->id);
    $this->model->revokeRoleTo($role->uuid);
    $this->model->revokeRoleTo($role->slug);
    $this->model->revokeRoleTo($role->name);

    expect($this->model->hasAnyRole($roles->pluck('id')->toArray()))->toBeFalse();
    expect($this->model->hasAnyRole($roles->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->model->hasAnyRole($roles->pluck('slug')->toArray()))->toBeFalse();
    expect($this->model->hasAnyRole($roles->pluck('name')->toArray()))->toBeFalse();
});

test('validate if the user has all the roles assigned', function () {
    $roles = Role::getAllFromCache();

    $role = $roles->shuffle()->first();

    $this->model->syncRoles($roles->pluck('id')->toArray());
    $this->model->syncRoles($roles->pluck('uuid')->toArray());
    $this->model->syncRoles($roles->pluck('slug')->toArray());
    $this->model->syncRoles($roles->pluck('name')->toArray());

    expect($this->model->hasAllRoles($roles->pluck('id')->toArray()))->toBeTrue();
    expect($this->model->hasAllRoles($roles->pluck('uuid')->toArray()))->toBeTrue();
    expect($this->model->hasAllRoles($roles->pluck('slug')->toArray()))->toBeTrue();
    expect($this->model->hasAllRoles($roles->pluck('name')->toArray()))->toBeTrue();

    $this->model->syncRoles([]);

    expect($this->model->hasAllRoles($roles->pluck('id')->toArray()))->toBeFalse();
    expect($this->model->hasAllRoles($roles->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->model->hasAllRoles($roles->pluck('slug')->toArray()))->toBeFalse();
    expect($this->model->hasAllRoles($roles->pluck('name')->toArray()))->toBeFalse();

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    expect($this->model->hasAllRoles($roles->pluck('id')->toArray()))->toBeFalse();
    expect($this->model->hasAllRoles($roles->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->model->hasAllRoles($roles->pluck('slug')->toArray()))->toBeFalse();
    expect($this->model->hasAllRoles($roles->pluck('name')->toArray()))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Permission
|--------------------------------------------------------------------------
*/

test('return role permissions correctly and in this case empty', function () {
    $permission = Permission::getAllFromCache()->shuffle()->first();

    $modelPermissions = $this->model->getPermissions();

    expect($modelPermissions)->toBeEmpty();
    expect($this->model->hasPermissionTo($permission->id))->toBeFalse();
    expect($this->model->hasPermissionTo($permission->uuid))->toBeFalse();
    expect($this->model->hasPermissionTo($permission->slug))->toBeFalse();

    $this->assertDatabaseMissing('model_permission', [
        'model_type' => User::class,
        'model_id'   => $this->model->id,
    ]);

    $permissions = collect()->range(1, 30);

    $permissions->each(
        fn () => expect($this->model->hasPermissionTo(Str::random(8)))->toBeFalse()
    );
});

test('add a permission for the role successfully', function () {
    $permission = Permission::getAllFromCache()->shuffle()->first();

    $this->model->assignPermissionTo($permission->id);
    $this->model->assignPermissionTo($permission->uuid);
    $this->model->assignPermissionTo($permission->slug);

    $modelPermissions = $this->model->getPermissions();

    expect($modelPermissions->count())->toBe(1);
    expect($this->model->hasPermissionTo($permission->id))->toBeTrue();
    expect($this->model->hasPermissionTo($permission->uuid))->toBeTrue();
    expect($this->model->hasPermissionTo($permission->slug))->toBeTrue();

    $this->assertDatabaseHas('model_permission', [
        'model_type'    => User::class,
        'model_id'      => $this->model->id,
        'permission_id' => $permission->id,
    ]);
});

test('remove role permissions as requested', function () {
    $permission = Permission::getAllFromCache()->shuffle()->first();

    $this->model->assignPermissionTo($permission->id);
    $this->model->assignPermissionTo($permission->uuid);
    $this->model->assignPermissionTo($permission->slug);

    expect($this->model->hasPermissionTo($permission->id))->toBeTrue();
    expect($this->model->hasPermissionTo($permission->uuid))->toBeTrue();
    expect($this->model->hasPermissionTo($permission->slug))->toBeTrue();

    $this->assertDatabaseHas('model_permission', [
        'model_type'    => User::class,
        'model_id'      => $this->model->id,
        'permission_id' => $permission->id,
    ]);

    $this->model->revokePermissionTo($permission->id);
    $this->model->revokePermissionTo($permission->uuid);
    $this->model->revokePermissionTo($permission->slug);

    $modelPermissions = $this->model->getPermissions();

    expect($modelPermissions)->toBeEmpty();
    expect($this->model->hasPermissionTo($permission->id))->toBeFalse();
    expect($this->model->hasPermissionTo($permission->uuid))->toBeFalse();
    expect($this->model->hasPermissionTo($permission->slug))->toBeFalse();

    $this->assertDatabaseMissing('model_permission', [
        'model_type'    => User::class,
        'model_id'      => $this->model->id,
        'permission_id' => $permission->id,
    ]);
});

test('sync permissions for the role', function () {
    $permissions = Permission::getAllFromCache();

    $this->model->syncPermissions($permissions->pluck('id')->toArray());
    $this->model->syncPermissions($permissions->pluck('uuid')->toArray());
    $this->model->syncPermissions($permissions->pluck('slug')->toArray());

    $modelPermissions = $this->model->getPermissions();

    $this->assertEquals($modelPermissions->toArray(), $permissions->toArray());
    expect($modelPermissions->count())->toBe($permissions->count());

    $permissions->each(function (Permission $permission) {
        expect($this->model->hasPermissionTo($permission->id))->toBeTrue();
        expect($this->model->hasPermissionTo($permission->uuid))->toBeTrue();
        expect($this->model->hasPermissionTo($permission->slug))->toBeTrue();

        $this->assertDatabaseHas('model_permission', [
            'model_type'    => User::class,
            'model_id'      => $this->model->id,
            'permission_id' => $permission->id,
        ]);
    });

    $this->model->syncPermissions([]);

    $this->assertDatabaseMissing('model_permission', [
        'model_type' => User::class,
        'model_id'   => $this->model->id,
    ]);
});

test('validate if the role has at least one permission assigned', function () {
    $permissions = Permission::getAllFromCache();

    $permission = $permissions->shuffle()->first();

    $this->model->assignPermissionTo($permission->id);
    $this->model->assignPermissionTo($permission->uuid);
    $this->model->assignPermissionTo($permission->slug);

    expect($this->model->hasAnyPermission($permissions->pluck('id')->toArray()))->toBeTrue();
    expect($this->model->hasAnyPermission($permissions->pluck('uuid')->toArray()))->toBeTrue();
    expect($this->model->hasAnyPermission($permissions->pluck('slug')->toArray()))->toBeTrue();

    $this->model->revokePermissionTo($permission->id);
    $this->model->revokePermissionTo($permission->uuid);
    $this->model->revokePermissionTo($permission->slug);

    expect($this->model->hasAnyPermission($permissions->pluck('id')->toArray()))->toBeFalse();
    expect($this->model->hasAnyPermission($permissions->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->model->hasAnyPermission($permissions->pluck('slug')->toArray()))->toBeFalse();
});

test('validate if the role has all the permissions assigned', function () {
    $permissions = Permission::getAllFromCache();

    $permission = $permissions->shuffle()->first();

    $this->model->syncPermissions($permissions->pluck('id')->toArray());
    $this->model->syncPermissions($permissions->pluck('uuid')->toArray());
    $this->model->syncPermissions($permissions->pluck('slug')->toArray());

    expect($this->model->hasAllPermissions($permissions->pluck('id')->toArray()))->toBeTrue();
    expect($this->model->hasAllPermissions($permissions->pluck('uuid')->toArray()))->toBeTrue();
    expect($this->model->hasAllPermissions($permissions->pluck('slug')->toArray()))->toBeTrue();

    $this->model->syncPermissions([]);

    expect($this->model->hasAllPermissions($permissions->pluck('id')->toArray()))->toBeFalse();
    expect($this->model->hasAllPermissions($permissions->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->model->hasAllPermissions($permissions->pluck('slug')->toArray()))->toBeFalse();

    $this->model->assignPermissionTo($permission->id);
    $this->model->assignPermissionTo($permission->uuid);
    $this->model->assignPermissionTo($permission->slug);

    expect($this->model->hasAllPermissions($permissions->pluck('id')->toArray()))->toBeFalse();
    expect($this->model->hasAllPermissions($permissions->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->model->hasAllPermissions($permissions->pluck('slug')->toArray()))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Permission Via Roles
|--------------------------------------------------------------------------
*/

test('return all user permissions through roles', function () {
    $roles = Role::getAllFromCache();

    $role = $roles->shuffle()->first();

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    $userPermissions = $this->model->getPermissionsByRole();

    $this->assertEquals($role->getPermissions(), $userPermissions);
    expect($userPermissions->count())->toBe($role->getPermissions()->count());

    $this->model->syncRoles($roles->pluck('id')->toArray());
    $this->model->syncRoles($roles->pluck('uuid')->toArray());
    $this->model->syncRoles($roles->pluck('slug')->toArray());
    $this->model->syncRoles($roles->pluck('name')->toArray());

    $userPermissions = $this->model->getPermissionsByRole();

    expect($userPermissions->count() >= $role->count())->toBeTrue();
});

test('check if the user has a permission from the roles', function () {
    $role = Role::getAllFromCache()->shuffle()->first();

    $permissions = $role->getPermissions();

    $permissions->each(fn ($permission) => expect($this->model->hasPermissionByRoleTo($permission->id))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasPermissionByRoleTo($permission->uuid))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasPermissionByRoleTo($permission->slug))->toBeFalse());

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    $permissions->each(fn ($permission) => expect($this->model->hasPermissionByRoleTo($permission->id))->toBeTrue());
    $permissions->each(fn ($permission) => expect($this->model->hasPermissionByRoleTo($permission->uuid))->toBeTrue());
    $permissions->each(fn ($permission) => expect($this->model->hasPermissionByRoleTo($permission->slug))->toBeTrue());
});

test('check if the user has any of the permissions from the roles', function () {
    $role = Role::getAllFromCache()->shuffle()->first();

    $permissions = $role->getPermissions();

    $permissions->each(fn ($permission) => expect($this->model->hasAnyPermissionByRole([$permission->id, Str::random(8)]))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasAnyPermissionByRole([$permission->uuid, Str::random(8)]))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasAnyPermissionByRole([$permission->slug, Str::random(8)]))->toBeFalse());

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    $permissions->each(fn ($permission) => expect($this->model->hasAnyPermissionByRole([$permission->id, Str::random(8)]))->toBeTrue());
    $permissions->each(fn ($permission) => expect($this->model->hasAnyPermissionByRole([$permission->uuid, Str::random(8)]))->toBeTrue());
    $permissions->each(fn ($permission) => expect($this->model->hasAnyPermissionByRole([$permission->slug, Str::random(8)]))->toBeTrue());
});

test('check if the user has all the permissions from the roles', function () {
    $role = Role::getAllFromCache()->shuffle()->first();

    $permissions  = $role->getPermissions();
    $permissions2 = $permissions->shuffle();

    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->id, $permissions2->random()->id]))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->uuid, $permissions2->random()->id]))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->slug, $permissions2->random()->slug]))->toBeFalse());

    $this->model->assignRoleTo($role->id);
    $this->model->assignRoleTo($role->uuid);
    $this->model->assignRoleTo($role->slug);
    $this->model->assignRoleTo($role->name);

    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->id, Str::random(8)]))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->uuid, Str::random(8)]))->toBeFalse());
    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->slug, Str::random(8)]))->toBeFalse());

    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->id, $permissions2->random()->id]))->toBeTrue());
    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->uuid, $permissions2->random()->id]))->toBeTrue());
    $permissions->each(fn ($permission) => expect($this->model->hasAllPermissionsByRole([$permission->slug, $permissions2->random()->slug]))->toBeTrue());
});
