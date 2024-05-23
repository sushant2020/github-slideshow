<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Activity;
use App\Models\UserActivity;
use Faker\Generator as Faker;

$factory->define(UserActivity::class, function (Faker $faker) {
    return [
        'activity_id' => function () {
            return factory(Activity::class)->create()->id;
        },
    ];
});