<?php

use Illuminate\Support\Facades\Artisan;

test('create role using the command', function () {
    $this->assertDatabaseMissing('roles', [
        'name' => 'Role Test Name',
        'slug' => 'role-test-name',
    ]);

    Artisan::call('anfragen:create-role "Role Test Name"');

    $this->assertDatabaseHas('roles', [
        'name' => 'Role Test Name',
        'slug' => 'role-test-name',
    ]);
});
