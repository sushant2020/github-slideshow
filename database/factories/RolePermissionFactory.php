<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Permission;
use App\Models\RolePermission;
use Faker\Generator as Faker;

$factory->define(RolePermission::class, function (Faker $faker) {
    return [
        'permission_id' => function () {
            return factory(Permission::class)->create()->id;
        },
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});