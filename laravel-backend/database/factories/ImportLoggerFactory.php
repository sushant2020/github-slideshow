<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\ImportLogger;
use Faker\Generator as Faker;

$factory->define(ImportLogger::class, function (Faker $faker) {
    return [
        'original_filename' => $faker->name,
        'filename' => $faker->name,
        'filesize' => $faker->word,
        'imported_by' => $faker->randomNumber(),
        'comment' => $faker->text,
        'output_message' => $faker->text,
        'uploaded_at' => $faker->datetime(),
        'imported_at' => $faker->datetime(),
    ];
});