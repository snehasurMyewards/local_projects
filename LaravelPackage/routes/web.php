<?php

use Illuminate\Support\Facades\Route;
use HelloWorld\Hello;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('hello/{user}', function ($user) {
    $hello = new Hello();
    dd($hello->user($user));
    //return view('welcome');
});