<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\BlogPostController;
use App\Http\Controllers\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(
    [
        'middleware' => 'api',
    ],
    function ($router) {

        Route::group(['middleware' => ['apikey']], function () {

            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/user-profile', [AuthController::class, 'userProfile']);
            Route::post('/change-password', [
                AuthController::class,
                'changepassword',
            ]);
            Route::put('/update-profile/{id}', [
                AuthController::class,
                'updateProfile',
            ]);

            Route::post('/forget-password', [
                AuthController::class,
                'forgetPassword',
            ]);
            Route::get('reset-password/{token}', [
                AuthController::class,
                'showResetPasswordForm',
            ])->name('reset.password.get');

            Route::post('/reset-password', [
                AuthController::class,
                'submitResetPasswordForm',
            ]);

            Route::post('/sore-blogpost', [
                BlogPostController::class,
                'storeBlogPost',
            ]);
            Route::put('/update-blogpost/{id}', [
                BlogPostController::class,
                'updateBlogPost',
            ]);
            Route::delete('/delete-blogpost/{id}', [
                BlogPostController::class,
                'deleteBlogPost',
            ]);
            Route::get('/view-blogpost', [
                BlogPostController::class,
                'viewBlogPost',
            ]);

            Route::post('/store-product', [
                ProductController::class,
                'storeProduct',
            ]);

            Route::post('/update-product/{id}', [
                ProductController::class,
                'updateProduct',
            ]);
            Route::Delete('/delete-product/{id}', [
                ProductController::class,
                'deleteProduct',
            ]);
            Route::get('/view-product', [
                ProductController::class,
                'viewProduct',
            ]);

        });
    }
);
