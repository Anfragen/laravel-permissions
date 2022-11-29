<?php

use Anfragen\Permission\Models\Role;
use Anfragen\Permission\Tests\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

test('return unauthorized access', function () {
    $role = Role::inRandomOrder()->first();

    Route::get('test-role', fn () => 'Success')->middleware('roles:' . $role->slug);

    $response = $this->getJson('/test-role')->assertForbidden();

    $response->assertJson(['message' => trans('anfragen::permissions.blocked')]);

    Route::get('test-role', fn () => 'Success')->middleware('roles:');

    $response = $this->getJson('/test-role')->assertForbidden();

    $response->assertJson(['message' => trans('anfragen::permissions.blocked')]);
});

test('authorize user request', function () {
    $role = Role::inRandomOrder()->first();

    $this->user->assignRoleTo($role->slug);

    Route::get('test-role', fn () => 'Success')->middleware('roles:' . $role->slug);

    $response = $this->get('/test-role')->assertOk();

    $this->assertEquals($response->getContent(), 'Success');
});

test('return unauthorized access with roles in message', function () {
    app('config')->set('permissions.roles_in_exception', true);

    $role = Role::inRandomOrder()->first();

    Route::get('test-role', fn () => 'Success')->middleware('roles:' . $role->slug);

    $response = $this->getJson('/test-role')->assertForbidden();

    $blocked = trans('anfragen::permissions.blocked');

    $blockRoles = trans('anfragen::permissions.block_roles', [
        'roles' => collect(Str::of($role->slug)->explode('|')->toArray())->implode(', '),
    ]);

    $response->assertJson(['message' => "{$blocked} {$blockRoles}"]);
});
