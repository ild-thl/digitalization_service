<?php

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

use Illuminate\Support\Facades\Auth;

// welcome page or redirect to home.
Route::get('/', function () {
    if(Auth::user()) {
        return redirect('home');
    } else {
        return view('welcome');
    }
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');

    // transformation routes
    Route::get('/transform', 'TransformController@transformStart');
    Route::post('/transform/upload', 'TransformController@transformUpload');
    Route::post('/transform/assignment', 'TransformController@transformAssignment');

    // elmo key routes
    Route::get('/elmo_keys', 'ElmoKeycontroller@showElmoKeys')->name('elmo_keys');

});
