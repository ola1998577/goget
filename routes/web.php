<?php

use App\Http\Controllers\driverController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\userController;
// use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

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



Auth::routes();


Route::group(['middleware' => ['auth', 'permission']], function() {
    /**
     * Logout Routes
     */

    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

    // Route::get('/logout', 'LogoutController@perform')->name('logout.perform');

    /**
     * User Routes
     */
    // Route::group(['prefix' => 'users'], function() {
    //     Route::get('/', 'UsersController@index')->name('users.index');
    //     Route::get('/create', 'UsersController@create')->name('users.create');
    //     Route::post('/create', 'UsersController@store')->name('users.store');
    //     Route::get('/{user}/show', 'UsersController@show')->name('users.show');
    //     Route::get('/{user}/edit', 'UsersController@edit')->name('users.edit');
    //     Route::patch('/{user}/update', 'UsersController@update')->name('users.update');
    //     Route::delete('/{user}/delete', 'UsersController@destroy')->name('users.destroy');
    //     // Route::resources('/',UsersController::class);
    // });

// Route::get('users',UsersController::class,'index')->name('users.index');
    // Route::resource('users',UsersController::class);

});
Route::resource('users',  userController::class);
Route::resource('drivers',  driverController::class);
Route::resource('roles', RoleController::class);


Route::get('locale/{locale}', function ($locale) {

    Session::put('locale', $locale);

    return redirect()->back();
});

//clear cache
Route::get('clear/route',function () {
    Artisan::call('optimize:clear');
 } );
