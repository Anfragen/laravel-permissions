<?php

namespace Anfragen\Permission\Tests\Models;

use Anfragen\Permission\Tests\Factories\UserFactory;
use Anfragen\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\{Factory, HasFactory};
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles;
    use HasFactory;

    /**
     * Get the factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
