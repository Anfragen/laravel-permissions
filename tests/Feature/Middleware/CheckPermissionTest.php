<?php

use Anfragen\Permission\Models\{Permission, Role};
use Anfragen\Permission\Tests\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

test('return unauthorized access', function () {
    $permission = Permission::inRandomOrder()->first();

    Route::get('test-permission', fn () => 'Success')->middleware('permissions:' . $permission->slug);

    $response = $this->getJson('/test-permission')->assertForbidden();

    $response->assertJson(['message' => trans('anfragen::permissions.blocked')]);

    Route::get('test-permission', fn () => 'Success')->middleware('permissions:');

    $response = $this->getJson('/test-permission')->assertForbidden();

    $response->assertJson(['message' => trans('anfragen::permissions.blocked')]);
});

test('authorize user request', function () {
    $role = Role::inRandomOrder()->first();

    $this->user->assignRoleTo($role->slug);

    $permission = Permission::factory()->create();

    $role->assignPermissionTo($permission->id);

    Route::get('test-permission', fn () => 'Success')->middleware('permissions:' . $permission->slug);

    $response = $this->get('/test-permission')->assertOk();

    $this->assertEquals($response->getContent(), 'Success');
});

test('return unauthorized access with permissions in message', function () {
    app('config')->set('permissions.permissions_in_exception', true);

    $permission = Permission::inRandomOrder()->first();

    Route::get('test-permission', fn () => 'Success')->middleware('permissions:' . $permission->slug);

    $response = $this->getJson('/test-permission')->assertForbidden();

    $blocked = trans('anfragen::permissions.blocked');

    $blockPermissions = trans('anfragen::permissions.block_permissions', [
        'permissions' => collect(Str::of($permission->slug)->explode('|')->toArray())->implode(', '),
    ]);

    $response->assertJson(['message' => "{$blocked} {$blockPermissions}"]);
});
