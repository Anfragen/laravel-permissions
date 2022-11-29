<?php

use Illuminate\Support\Facades\Artisan;

test('create permission using the command', function () {
    $this->assertDatabaseMissing('permissions', [
        'group' => 'Group',
        'name'  => 'Test Name',
        'slug'  => 'group-test-name',
    ]);

    Artisan::call('anfragen:create-permission Group "Test Name"');

    $this->assertDatabaseHas('permissions', [
        'group' => 'Group',
        'name'  => 'Test Name',
        'slug'  => 'group-test-name',
    ]);
});
