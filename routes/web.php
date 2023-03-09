<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;
use App\Models\User;
use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

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
    return view('welcome');
});
Route::get('/test', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::post('loginProcess', [AdminController::class, 'loginProcess'])->name('loginProcess');
Route::get('logout', [AdminController::class, 'logout']);

