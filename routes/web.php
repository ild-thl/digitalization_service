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

$enable_register = env('DIGISERV_REGISTER', false);
if($enable_register) {
    Auth::routes();
} else {
    Auth::routes(['register' => false]);
}

// validation routes
Route::get('/validate', 'ValidateController@showValidateForm')->name('validate');
Route::post('/validate', 'ValidateController@showValidateResult');

Route::middleware(['auth'])->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');

    // transformation routes
    Route::get('/transform', 'TransformController@transformStart')->name('transform');
    Route::get('/transform/assign', function () { return redirect()->route('transform'); });
    Route::post('/transform/assign', 'TransformController@transformAssign')->name('transform/assign');
    Route::get('/transform/create', function () { return redirect()->route('transform'); });
    Route::post('/transform/create', 'TransformController@transformCreate')->name('transform/create');
    Route::get('/transform/download', function () { return redirect()->route('transform'); });
    Route::post('/transform/download', 'TransformController@transformDownload')->name('transform/download');

    // elmo key routes
    Route::get('/elmo_keys', 'ElmoKeyController@showElmoKeys')->name('elmo_keys');
    Route::post('elmo_keys/add', 'ElmoKeyController@addElmoKey')->name('elmo_keys/add');
    Route::post('elmo_keys/delete', 'ElmoKeyController@deleteElmoKey')->name('elmo_keys/delete');

    // key assignments routes
    Route::get('/key_assignments', 'KeyAssignmentController@showKeyAssignments')->name('key_assignments');
    Route::post('/key_assignments/delete', 'KeyAssignmentController@deleteKeyAssignment')->name('key_assignments/delete');

});
