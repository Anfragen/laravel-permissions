<?php

namespace Anfragen\Permission\Factories;

use Anfragen\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * Specifies the model that this factory represents.
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'group' => $this->faker->unique()->word(),
            'name'  => $this->faker->word(),
            'slug'  => $this->faker->unique()->slug(3),
        ];
    }
}
