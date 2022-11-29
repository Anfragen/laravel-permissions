<?php

use Anfragen\Permission\Models\{Permission, Role};
use Illuminate\Support\Str;

beforeEach(function () {
    $this->role = Role::inRandomOrder()->first();

    $this->role->syncPermissions([]);
});

test('check if the permissions are cached correctly', function () {
    $permissions      = Permission::all();
    $permissionsCache = Permission::getAllFromCache();

    $this->assertEquals($permissions, $permissionsCache);
    expect($permissions->count())->toBe($permissionsCache->count());

    $random = Permission::inRandomOrder()->first();

    $this->assertEquals($random->toArray(), Permission::getPermission($random->id)->toArray());
    $this->assertEquals($random->toArray(), Permission::getPermission($random->uuid)->toArray());
    $this->assertEquals($random->toArray(), Permission::getPermission($random->slug)->toArray());
});

test('return role permissions correctly and in this case empty', function () {
    $permission = Permission::getAllFromCache()->shuffle()->first();

    $rolePermissions = $this->role->getPermissions();

    expect($rolePermissions)->toBeEmpty();
    expect($this->role->hasPermissionTo($permission->id))->toBeFalse();
    expect($this->role->hasPermissionTo($permission->uuid))->toBeFalse();
    expect($this->role->hasPermissionTo($permission->slug))->toBeFalse();

    $this->assertDatabaseMissing('permission_role', ['role_id' => $this->role->id]);

    $permissions = collect()->range(1, 30);

    $permissions->each(
        fn () => expect($this->role->hasPermissionTo(Str::random(8)))->toBeFalse()
    );
});

test('add a permission for the role successfully', function () {
    $permission = Permission::getAllFromCache()->shuffle()->first();

    $this->role->assignPermissionTo($permission->id);
    $this->role->assignPermissionTo($permission->uuid);
    $this->role->assignPermissionTo($permission->slug);

    $rolePermissions = $this->role->getPermissions();

    expect($rolePermissions->count())->toBe(1);
    expect($this->role->hasPermissionTo($permission->id))->toBeTrue();
    expect($this->role->hasPermissionTo($permission->uuid))->toBeTrue();
    expect($this->role->hasPermissionTo($permission->slug))->toBeTrue();

    $this->assertDatabaseHas('permission_role', [
        'role_id'       => $this->role->id,
        'permission_id' => $permission->id,
    ]);
});

test('remove role permissions as requested', function () {
    $permission = Permission::getAllFromCache()->shuffle()->first();

    $this->role->assignPermissionTo($permission->id);
    $this->role->assignPermissionTo($permission->uuid);
    $this->role->assignPermissionTo($permission->slug);

    expect($this->role->hasPermissionTo($permission->id))->toBeTrue();
    expect($this->role->hasPermissionTo($permission->uuid))->toBeTrue();
    expect($this->role->hasPermissionTo($permission->slug))->toBeTrue();

    $this->assertDatabaseHas('permission_role', [
        'role_id'       => $this->role->id,
        'permission_id' => $permission->id,
    ]);

    $this->role->revokePermissionTo($permission->id);
    $this->role->revokePermissionTo($permission->uuid);
    $this->role->revokePermissionTo($permission->slug);

    $rolePermissions = $this->role->getPermissions();

    expect($rolePermissions)->toBeEmpty();
    expect($this->role->hasPermissionTo($permission->id))->toBeFalse();
    expect($this->role->hasPermissionTo($permission->uuid))->toBeFalse();
    expect($this->role->hasPermissionTo($permission->slug))->toBeFalse();

    $this->assertDatabaseMissing('permission_role', [
        'role_id'       => $this->role->id,
        'permission_id' => $permission->id,
    ]);
});

test('sync permissions for the role', function () {
    $permissions = Permission::getAllFromCache();

    $this->role->syncPermissions($permissions->pluck('id')->toArray());
    $this->role->syncPermissions($permissions->pluck('uuid')->toArray());
    $this->role->syncPermissions($permissions->pluck('slug')->toArray());

    $rolePermissions = $this->role->getPermissions();

    $this->assertEquals($rolePermissions->toArray(), $permissions->toArray());
    expect($rolePermissions->count())->toBe($permissions->count());

    $permissions->each(function (Permission $permission) {
        expect($this->role->hasPermissionTo($permission->id))->toBeTrue();
        expect($this->role->hasPermissionTo($permission->uuid))->toBeTrue();
        expect($this->role->hasPermissionTo($permission->slug))->toBeTrue();

        $this->assertDatabaseHas('permission_role', [
            'role_id'       => $this->role->id,
            'permission_id' => $permission->id,
        ]);
    });

    $this->role->syncPermissions([]);

    $this->assertDatabaseMissing('permission_role', ['role_id' => $this->role->id]);
});

test('validate if the role has at least one permission assigned', function () {
    $permissions = Permission::getAllFromCache();

    $permission = $permissions->shuffle()->first();

    $this->role->assignPermissionTo($permission->id);
    $this->role->assignPermissionTo($permission->uuid);
    $this->role->assignPermissionTo($permission->slug);

    expect($this->role->hasAnyPermission($permissions->pluck('id')->toArray()))->toBeTrue();
    expect($this->role->hasAnyPermission($permissions->pluck('uuid')->toArray()))->toBeTrue();
    expect($this->role->hasAnyPermission($permissions->pluck('slug')->toArray()))->toBeTrue();

    $this->role->revokePermissionTo($permission->id);
    $this->role->revokePermissionTo($permission->uuid);
    $this->role->revokePermissionTo($permission->slug);

    expect($this->role->hasAnyPermission($permissions->pluck('id')->toArray()))->toBeFalse();
    expect($this->role->hasAnyPermission($permissions->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->role->hasAnyPermission($permissions->pluck('slug')->toArray()))->toBeFalse();
});

test('validate if the role has all the permissions assigned', function () {
    $permissions = Permission::getAllFromCache();

    $permission = $permissions->shuffle()->first();

    $this->role->syncPermissions($permissions->pluck('id')->toArray());
    $this->role->syncPermissions($permissions->pluck('uuid')->toArray());
    $this->role->syncPermissions($permissions->pluck('slug')->toArray());

    expect($this->role->hasAllPermissions($permissions->pluck('id')->toArray()))->toBeTrue();
    expect($this->role->hasAllPermissions($permissions->pluck('uuid')->toArray()))->toBeTrue();
    expect($this->role->hasAllPermissions($permissions->pluck('slug')->toArray()))->toBeTrue();

    $this->role->syncPermissions([]);

    expect($this->role->hasAllPermissions($permissions->pluck('id')->toArray()))->toBeFalse();
    expect($this->role->hasAllPermissions($permissions->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->role->hasAllPermissions($permissions->pluck('slug')->toArray()))->toBeFalse();

    $this->role->assignPermissionTo($permission->id);
    $this->role->assignPermissionTo($permission->uuid);
    $this->role->assignPermissionTo($permission->slug);

    expect($this->role->hasAllPermissions($permissions->pluck('id')->toArray()))->toBeFalse();
    expect($this->role->hasAllPermissions($permissions->pluck('uuid')->toArray()))->toBeFalse();
    expect($this->role->hasAllPermissions($permissions->pluck('slug')->toArray()))->toBeFalse();
});
