<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdviceController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductCommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
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
Route::post( 'auth/check.username', [ LoginController::class, 'checkUsername' ] );
Route::post( 'auth/login', [ LoginController::class, 'login' ] );
Route::post( 'auth/logout', [ LoginController::class, 'logout' ] );
Route::post( 'auth/send.verify.code', [ RegisterController::class, 'sendVerifyCode' ] );
Route::post( 'auth/check.verify.code', [ RegisterController::class, 'checkVerifyCode' ] );
Route::post( 'auth/register', [ RegisterController::class, 'register' ] );
Route::get( 'user', [ UserController::class, 'currentUser' ] );
// Store //
Route::apiResource( 'attributes', AttributeController::class );
Route::apiResource( 'brands', BrandController::class );
Route::apiResource( 'storeCategories', StoreCategoryController::class )->parameters( [ 'storeCategories' => 'category' ] );
Route::apiResource( 'products', ProductController::class )->whereNumber( 'product' );
Route::get( 'products/{slug}', [ ProductController::class, 'show' ] );
Route::get( 'products/{product:slug}/toggle_withlist', [ ProductController::class, 'toggleWishlist' ] );

Route::apiResource( 'product/comments', ProductCommentController::class )->parameters( [ 'product/comments' => 'comment' ] );
Route::put( 'product/comments/{comment}/toggleConfirm', [ ProductCommentController::class, 'toggleConfirm' ] );
Route::group( [ 'prefix' => 'carts' ], function () {
	Route::get('/',[CartController::class,'index']);
	Route::get('/{cart}',[CartController::class,'show']);
	Route::group( [ 'prefix' => 'currentCart' ], function () {
		Route::post( 'add', [ CartController::class, 'addToCart' ] );
		Route::get( '', [ CartController::class, 'currentCart' ] );
		Route::get( '{productvariant:id}', [ CartController::class, 'removeFromCart' ] );
		Route::post( 'setAddress/{address}', [ CartController::class, 'setAddress' ] );
	} );

} );
Route::group(['prefix'=>'orders'],function (){
	Route::get('payCart',[\App\Http\Controllers\OrderController::class,'payCart']);
	Route::get('checkPayment',[\App\Http\Controllers\OrderController::class,'checkPayment']);
	Route::get('',[\App\Http\Controllers\OrderController::class,'index']);
	Route::get('/{order}',[\App\Http\Controllers\OrderController::class,'show']);
	Route::put('/{order}',[\App\Http\Controllers\OrderController::class,'saveChange']);
});
//--User--//
Route::apiResource( 'users', UserController::class )->except( 'store' );
//--admin--//
Route::apiResource( 'admins', AdminController::class )->parameters( [ 'admins' => 'user' ] );
//-- Blog --//
Route::apiResource( 'articles', ArticleController::class )->whereNumber( 'article' );
Route::get( 'articles/{slug}', [ ArticleController::class, 'show' ] );
Route::get( 'tags', [ ArticleController::class, 'tags' ] );
Route::apiResource( 'articleCategories', ArticleCategoryController::class )->parameters( [ 'articleCategories' => 'category' ] );
Route::get( 'categoryArticle', '\App\Http\Controllers\ArticleCategoryController@categoryArticle' );
Route::apiResource( 'articleComments', ArticleCommentController::class )->parameters( [ 'articleComments' => 'comment' ] )->scoped( [ 'comment' => 'id' ] );
Route::put( 'articleComments/{comment}/toggleConfirm', [ ArticleCommentController::class, 'toggleConfirm' ] );
//--Profile--//
Route::group( [ 'prefix' => 'profile/' ], function () {
	Route::get( 'user', [ ProfileController::class, 'user' ] );
	Route::match( [ 'put', 'patch' ], 'update', [ ProfileController::class, 'update' ] );
	Route::put( 'update/avatar', [ ProfileController::class, 'updateAvatar' ] );
	Route::put( 'password', [ ProfileController::class, 'password' ] );
	Route::group( [ 'prefix' => 'address' ], function () {
		Route::get( '', [ AddressController::class, 'index' ] );
		Route::post( '', [ AddressController::class, 'store' ] );
		Route::delete( '/{address}', [ AddressController::class, 'delete' ] );
		Route::put( '/{address}', [ AddressController::class, 'update' ] );
	} );
	Route::get( 'wishlist', [ WishlistController::class, 'index' ] );
	Route::get( 'lastWishlist', [ WishlistController::class, 'lastWishlist' ] );

} );
// Forms //
Route::group( [ 'prefix' => 'forms' ], function () {
	Route::group( [ 'prefix' => 'advices' ], function () {
		Route::post( '/', [ AdviceController::class, 'storeAdvice' ] );
		Route::get( '/', [ AdviceController::class, 'showAdvice' ] );
		Route::delete( '/{advice}', [ AdviceController::class, 'deleteAdvice' ] );
		Route::put( '/{advice}/toggleCheck', [ AdviceController::class, 'toggleCheck' ] );
	} );
	Route::group( [ 'prefix' => 'customizations' ], function () {
		Route::get( '/', [ \App\Http\Controllers\CustomizationController::class, 'index' ] );
		Route::post( '/', [ \App\Http\Controllers\CustomizationController::class, 'storeCustomization' ] );
		Route::delete( '/{customization}', [ \App\Http\Controllers\CustomizationController::class, 'deleteCuctomization' ] );
		Route::put( '/{customization}/toggleStatus', [ \App\Http\Controllers\CustomizationController::class, 'toggleStatus' ] );
	} );
} );
// App //
Route::group( [ 'prefix' => 'files' ], function () {
	Route::post( 'upload', [ FileController::class, 'upload' ] );
} );
Route::apiResource( 'roles', RoleController::class );
Route::get( 'permissions', [ RoleController::class, 'permissions' ] );
// Settings //
Route::apiResource( 'pages', PageController::class )->whereNumber( 'page' );
Route::get( 'pages/{slug}', [ PageController::class, 'show' ] );
Route::group( [ 'prefix' => 'settings' ], function () {
	Route::get( 'header', [ SettingController::class, 'getHeader' ] );
	Route::put( 'header', [ SettingController::class, 'updateHeader' ] );
	Route::get( 'footer', [ SettingController::class, 'getFooter' ] );
	Route::put( 'footer', [ SettingController::class, 'updateFooter' ] );
	Route::get( 'home', [ SettingController::class, 'getHome' ] );
	Route::put( 'home', [ SettingController::class, 'updateHome' ] );
} );
