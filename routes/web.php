<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['hasAdminRole'])->group(function(){
    Route::get('/admin', 'AdminController@index')->name('admin');
    Route::get('/admin/users', 'AdminController@users')->name('admin-users');
    Route::post('/admin/users/activate', 'AdminController@activateUser')->name('admin-activateUser');
    Route::post('/admin/users/deactivate', 'AdminController@deactivateUser')->name('admin-deactivateUser');
    Route::post('/admin/users/changeRole', 'AdminController@changeRoleForUser')->name('admin-changeUserRole');

    Route::get('/admin/courses', 'AdminController@courses')->name('admin-courses');
    Route::get('/admin/courses/getParticipants/{id}', 'AdminController@getCourseParticipants')->name('admin-getCourseParticipants');

    Route::get('/admin/topics', 'AdminController@topics')->name('admin-topics');
});
