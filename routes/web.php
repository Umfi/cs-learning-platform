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
    Route::post('/admin/courses/activate', 'AdminController@activateCourse')->name('admin-activateCourse');
    Route::post('/admin/courses/deactivate', 'AdminController@deactivateCourse')->name('admin-deactivateCourse');
    Route::get('/admin/courses/getParticipants/{id}', 'AdminController@getCourseParticipants')->name('admin-getCourseParticipants');

    Route::get('/admin/topics', 'AdminController@topics')->name('admin-topics');
});


Route::middleware(['hasTeacherRole'])->group(function(){
    Route::get('/teacher/getCourse/{id}', 'TeacherController@getCourseData')->name('teacher-getCourse');
    Route::post('/teacher/createCourse', 'TeacherController@createCourse')->name('teacher-createCourse');
    Route::post('/teacher/editCourse', 'TeacherController@editCourse')->name('teacher-editCourse');

    Route::get('/teacher/course/{id}', 'TeacherController@showCourse')->name('teacher-showCourse');
    Route::get('/teacher/getTopic/{id}', 'TeacherController@getTopicData')->name('teacher-getTopic');
    Route::post('/teacher/createTopic', 'TeacherController@createTopic')->name('teacher-createTopic');
    Route::post('/teacher/editTopic', 'TeacherController@editTopic')->name('teacher-editTopic');

    Route::get('/teacher/topic/{id}', 'TeacherController@showTopic')->name('teacher-showTopic');
    Route::get('/teacher/getTask/{id}', 'TeacherController@getTaskData')->name('teacher-getTask');
    Route::post('/teacher/createTask', 'TeacherController@createTask')->name('teacher-createTask');
    Route::post('/teacher/editTask', 'TeacherController@editTask')->name('teacher-editTask');

});
