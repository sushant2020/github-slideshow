<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Comment;
use App\Models\ProductComment;
use Faker\Generator as Faker;

$factory->define(ProductComment::class, function (Faker $faker) {
    return [
        'comment_id' => function () {
            return factory(Comment::class)->create()->id;
        },
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
    ];
});