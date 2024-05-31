<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;




// Catch-all route for React Router
Route::get('/{any}', function () {
    return File::get(public_path() . '/index.html');
})->where('any', '.*');



Route::view('forgot_password', 'auth.reset_password')->name('password.reset');