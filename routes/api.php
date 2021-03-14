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

Route::apiResource('attributes' , AttributeController::class);
Route::apiResource('products' , ProductController::class);
Route::apiResource('brands' , BrandController::class);
