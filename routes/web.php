<?php

use Illuminate\Support\Facades\Route;

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
    // return view('welcome');
    return md5('@robert');
    // @11@22@33: e4a0e84beb78fbdbe483a55df77e25f4
    // dairo : dscd2024.  : 96aba923e9811668b0635ddea1b6a365
    // daramola : @robert :  e7888709bbb4583d1601600e2675df0d
});
