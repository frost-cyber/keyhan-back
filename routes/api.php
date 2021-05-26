<?php

use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductCommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;

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

// Auth //
Route::post('auth/check.username', [LoginController::class, 'checkUsername']);
Route::post('auth/login', [LoginController::class, 'login']);
Route::post('auth/logout', [LoginController::class, 'logout']);
Route::post('auth/send.verify.code', [RegisterController::class, 'sendVerifyCode']);
Route::post('auth/check.verify.code', [RegisterController::class, 'checkVerifyCode']);
Route::post('auth/register', [RegisterController::class, 'register']);
Route::get('user', [UserController::class, 'currentUser']);
// Store //
Route::apiResource('attributes', AttributeController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('storeCategories', StoreCategoryController::class)->parameters(['storeCategories' => 'category']);
Route::apiResource('products', ProductController::class)->whereNumber('product');
Route::get('products/{slug}', [ProductController::class, 'show']);
Route::get('products/{product:slug}/toggle_withlist', [ProductController::class, 'toggleWishlist']);

Route::apiResource('product/comments', ProductCommentController::class)->parameters(['product/comments' => 'comment']);
Route::put('product/comments/{comment}/toggleConfirm', [ProductCommentController::class, 'toggleConfirm']);

//-- Blog --//
Route::apiResource('articles', ArticleController::class)->whereNumber('article');
Route::get('articles/{slug}', [ArticleController::class, 'show']);
Route::get('tags', [ArticleController::class, 'tags']);
Route::apiResource('articleCategories', ArticleCategoryController::class)->parameters(['articleCategories' => 'category']);
Route::get('categoryArticle', '\App\Http\Controllers\ArticleCategoryController@categoryArticle');
Route::apiResource('articleComments', ArticleCommentController::class)->parameters(['articleComments' => 'comment'])->scoped(['comment' => 'id']);
Route::put('articleComments/{comment}/toggleConfirm', [ArticleCommentController::class, 'toggleConfirm']);
//--Profile--//
Route::group(['prefix' => 'profile/'], function () {
    Route::get('user', [\App\Http\Controllers\ProfileController::class, 'user']);
    Route::match(['put', 'patch'], 'update', [\App\Http\Controllers\ProfileController::class, 'update']);
    Route::put('update/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar']);
    Route::put('password', [\App\Http\Controllers\ProfileController::class, 'password']);
    Route::group(['prefix' => 'address'], function () {
        Route::get('', [\App\Http\Controllers\AddressController::class, 'index']);
        Route::post('', [\App\Http\Controllers\AddressController::class, 'store']);
        Route::delete('/{address}', [\App\Http\Controllers\AddressController::class, 'delete']);
        Route::put('/{address}', [\App\Http\Controllers\AddressController::class, 'update']);
    });
    Route::get('wishlist', [\App\Http\Controllers\WishlistController::class, 'index']);
    Route::get('lastWishlist', [\App\Http\Controllers\WishlistController::class, 'lastWishlist']);

});

// App //
Route::group(['prefix' => 'files'], function () {
    Route::post('upload', [FileController::class, 'upload']);
});
// Settings //
Route::post('advice', [\App\Http\Controllers\AdviceController::class, 'storeAdvice']);
Route::get('showallAdvices', [\App\Http\Controllers\AdviceController::class, 'showAdvice']);
Route::delete('delete/{advice}', [\App\Http\Controllers\AdviceController::class, 'deleteAdvice']);
Route::put('advice/{advice}/toggleCheck', [\App\Http\Controllers\AdviceController::class, 'toggleCheck']);

Route::group(['prefix' => 'settings'], function () {
    Route::get('header', [SettingController::class, 'getHeader']);
    Route::put('header', [SettingController::class, 'updateHeader']);
    Route::get('footer', [SettingController::class, 'getFooter']);
    Route::put('footer', [SettingController::class, 'updateFooter']);
    Route::get('home', [SettingController::class, 'getHome']);
    Route::put('home', [SettingController::class, 'updateHome']);
});
