<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\cartController;
use App\Http\Controllers\api\favouriteController;
use App\Http\Controllers\api\homeController;
use App\Http\Controllers\api\orderController;
use App\Http\Controllers\api\settingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//setting
Route::get('get-setting',[settingController::class,'get_setting']);
Route::get('privacy-policy',[settingController::class,'privacy_policy']);
Route::get('term-condition',[settingController::class,'term_condition']);
Route::get('about-us',[settingController::class,'about_us']);
Route::post('contact-us',[settingController::class,'contact_us']);
Route::post('notification',[settingController::class,'change_notification']);

//authentication
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::get('profile',[AuthController::class,'profile']);
Route::post('update-profile',[AuthController::class,'update_profile']);

//home
Route::get('home',[homeController::class,'home']);
Route::get('products',[homeController::class,'products']);
Route::get('/details-product', [homeController::class, 'show']);

//store
Route::get('all-store',[homeController::class,'all_store']);
Route::get('all-category',[homeController::class,'all_category']);


//order
Route::get('my-order',[orderController::class,'my_order']);
Route::post('createOrder',[orderController::class,'createOrder']);

//cart
Route::get('store-carts',[cartController::class,'index']);
Route::get('product-carts',[cartController::class,'getProductsByStore']);
Route::post('addToCart',[cartController::class,'addToCart']);
Route::delete('removeFromCart',[cartController::class,'removeFromCart']);
Route::post('updateCart',[cartController::class,'updateCart']);

//favourite
Route::get('favourite',[favouriteController::class,'index']);
Route::post('addToFavourite',[favouriteController::class,'addToFavourite']);
Route::post('removeFromFavourite',[favouriteController::class,'removeFromFavourite']);

