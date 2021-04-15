<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreCategoryController;
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

Route::post('auth/check.username' , [\App\Http\Controllers\Auth\LoginController::class , 'checkUsername']);
Route::post('auth/login' , [\App\Http\Controllers\Auth\LoginController::class , 'login']);
Route::post('auth/send.verify.code' , [\App\Http\Controllers\Auth\RegisterController::class , 'sendVerifyCode']);
Route::post('auth/check.verify.code' , [\App\Http\Controllers\Auth\RegisterController::class , 'checkVerifyCode']);
Route::post('auth/register' , [\App\Http\Controllers\Auth\RegisterController::class , 'register']);

Route::get('user' , function(){
    return auth()->user();
})->middleware('auth:sanctum');

Route::apiResource('categories' , StoreCategoryController::class);
Route::apiResource('articleCategories' , \App\Http\Controllers\ArticleCategoryController::class)->parameters(['articleCategories' => 'category']);
Route::get('categoryArticle','\App\Http\Controllers\ArticleCategoryController@categoryArticle');
Route::apiResource('attributes' , AttributeController::class);
Route::get('products/{product:slug}' , [ProductController::class , 'show'])->whereAlpha('product');
Route::apiResource('products' , ProductController::class)->whereNumber('product');
Route::apiResource('brands' , BrandController::class);
Route::get('tags',[\App\Http\Controllers\ArticleController::class,'tags']);
Route::put('articleComments/{comment}/toggleConfirm',[\App\Http\Controllers\ArticleCommentController::class,'toggleConfirm']);
Route::apiResource('articleComments' , \App\Http\Controllers\ArticleCommentController::class)->scoped(['comment'=>'id']);
Route::apiResource('articles',\App\Http\Controllers\ArticleController::class)->scoped(['article' =>'slug']);


Route::group(['prefix' => 'files'] , function(){
    Route::post('upload' , [\App\Http\Controllers\FileController::class , 'upload']);
});
