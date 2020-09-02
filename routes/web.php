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
    if (Auth::check()) {
        return redirect()->route('home');
    } else {
        return view('welcome');
    }
})->name('home');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profile', 'ProfileController@index')->name('profile');
Route::post('/profile/update', 'ProfileController@updateProfile')->name('updateProfile');

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
    Route::post('/admin/topics/activate', 'AdminController@activateTopic')->name('admin-activateTopic');
    Route::post('/admin/topics/deactivate', 'AdminController@deactivateTopic')->name('admin-deactivateTopic');

    Route::get('/admin/tasks', 'AdminController@tasks')->name('admin-tasks');
    Route::post('/admin/tasks/activate', 'AdminController@activateTask')->name('admin-activateTask');
    Route::post('/admin/tasks/deactivate', 'AdminController@deactivateTask')->name('admin-deactivateTask');

    Route::get('/admin/ratings', 'AdminController@ratings')->name('admin-ratings');
});

Route::middleware(['hasTeacherRole'])->group(function(){
    Route::get('/teacher/getCourse/{id}', 'TeacherController@getCourseData')->name('teacher-getCourse');
    Route::post('/teacher/createCourse', 'TeacherController@createCourse')->name('teacher-createCourse');
    Route::post('/teacher/editCourse', 'TeacherController@editCourse')->name('teacher-editCourse');
    Route::post('/teacher/copyCourse', 'TeacherController@copyCourse')->name('teacher-copyCourse');
    Route::delete('/teacher/deleteCourse/{id}', 'TeacherController@deleteCourse')->name('teacher-deleteCourse');


    Route::get('/teacher/course/{id}', 'TeacherController@showCourse')->name('teacher-showCourse');
    Route::get('/teacher/getTopic/{id}', 'TeacherController@getTopicData')->name('teacher-getTopic');
    Route::post('/teacher/createTopic', 'TeacherController@createTopic')->name('teacher-createTopic');
    Route::post('/teacher/editTopic', 'TeacherController@editTopic')->name('teacher-editTopic');
    Route::delete('/teacher/deleteTopic/{id}', 'TeacherController@deleteTopic')->name('teacher-deleteTopic');


    Route::get('/teacher/topic/{id}', 'TeacherController@showTopic')->name('teacher-showTopic');
    Route::get('/teacher/getTask/{id}', 'TeacherController@getTaskData')->name('teacher-getTask');
    Route::post('/teacher/createTask', 'TeacherController@createTask')->name('teacher-createTask');
    Route::post('/teacher/editTask', 'TeacherController@editTask')->name('teacher-editTask');
    Route::post('/teacher/setTaskModuleConfig/{id}', 'TeacherController@setTaskModuleConfig')->name('teacher-setTaskModuleConfig');
    Route::delete('/teacher/deleteTask/{id}', 'TeacherController@deleteTask')->name('teacher-deleteTask');
    Route::get('/teacher/topic/{id}/learningpath', 'TeacherController@showLearningPath')->name('teacher-learningPath');
    Route::post('/teacher/topic/{id}/storeLearningPath', 'TeacherController@storeLearningPath')->name('teacher-storeLearningPath');

    Route::get('/teacher/getAllRatingsFromCourse/{id}', 'TeacherController@getAllRatingsFromCourse')->name('teacher-getAllRatingsFromCourse');

});

Route::post('/student/joinCourse', 'StudentController@joinCourse')->name('student-joinCourse');
Route::get('/student/course/{id}', 'StudentController@showCourse')->name('student-showCourse');
Route::get('/student/topic/{id}', 'StudentController@showTopic')->name('student-showTopic');

Route::get('/student/getTask/{id}', 'StudentController@getTaskData')->name('student-getTask');
Route::post('/student/solveTask/{id}', 'StudentController@solveTask')->name('student-solveTask');

