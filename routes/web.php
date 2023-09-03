<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // dd(App\Models\Properties::latest()->paginate(6));
    return view('welcome', [
        'properties' => App\Models\Properties::latest()->paginate(1),
    ]);
});
