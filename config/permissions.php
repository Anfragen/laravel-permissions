<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Exception Handling
    |--------------------------------------------------------------------------
    |
    | Here you will configure if you want to show the
    | permissions and roles that gave error in the exceptions.
    |
    */

    'roles_in_exception' => false,

    'permissions_in_exception' => false,

    /*
    |--------------------------------------------------------------------------
    | Cache Configurations
    |--------------------------------------------------------------------------
    |
    | Here are caching related settings as all permission
    | system is added in cache for better performance.
    |
    */

    'cache' => [
        'prefix' => 'anfragen::permissions',

        'expiration_time' => 1440, // in minutes
    ],

];
