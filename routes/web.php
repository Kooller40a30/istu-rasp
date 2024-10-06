<?php

use App\Http\Controllers\ClassroomsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\StartController;
use App\Http\Controllers\TeachersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UploadController;

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

//начало
//главная страница
Route::get('/', [StartController::class, 'index'])->name('start');

Route::get('/groups_schedule', [GroupsController::class, 'loadGroupSchedule'])->name('groups_schedule');
// Route::post('/', [TeachersController::class, 'loadTeacherSchedule'])->name('teachers_schedule');
// Route::post('/', [ClassroomsController::class, 'loadClassroomSchedule'])->name('classrooms_schedule');

//расписание преподавателей
// Route::get('/teachers', [TeachersController::class, 'getAllTeachers'])->name('get_teachers');
Route::post('/teachers_faculty', [TeachersController::class, 'getFacultyTeachers'])->name('teachers_faculty');
Route::post('/teachers_department', [TeachersController::class, 'getDepartmentTeachers'])->name('teachers_department');
Route::post('/teachers_teacher', [TeachersController::class, 'getTeacher'])->name('teachers_teacher');
Route::get('/teacher_schedule', [TeachersController::class, 'loadTeacherSchedule'])->name('teacher_schedule');

//расписание аудиторий
Route::get('/classroom', [ClassroomsController::class, 'getAllClassrooms'])->name('get_classrooms');
Route::post('/classrooms_faculty', [ClassroomsController::class, 'getFacultyClassrooms'])->name('classrooms_faculty');
Route::post('/classrooms_department', [ClassroomsController::class, 'getDepartmentClassrooms'])->name('classrooms_department');
Route::post('/classrooms_classroom', [ClassroomsController::class, 'getClassroom'])->name('classrooms_classroom');
Route::get('/classrooms', [ClassroomsController::class, 'getClassrooms'])->name('getClassrooms');
Route::get('/classroom_schedule', [ClassroomsController::class, 'loadClassroomSchedule'])->name('classroom_schedule');

//расписание групп
// Route::get('/groups', [GroupsController::class, 'getAllGroups'])->name('get_groups');
Route::post('/groups_faculty', [GroupsController::class, 'getFacultyGroups'])->name('groups_faculty');
Route::post('/groups_course', [GroupsController::class, 'getCourse'])->name('groups_course');
Route::post('/groups_group', [GroupsController::class, 'getGroup'])->name('groups_group');
Route::get('/groups', [GroupsController::class, 'getGroups'])->name('getGroups');

// курс обучения
Route::get('/courses', [CoursesController::class, 'getCourses'])->name('getCourses');

// кафедры
Route::get('/departments', [DepartmentController::class, 'getDepartments'])->name('getDepartments');

// преподаватели
Route::get('/teachers', [TeachersController::class, 'getTeachers'])->name('getTeachers');

//аутентификация
Route::any('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/upload_files', [UploadController::class, 'uploadFiles'])->name('upload_files');

Route::get('/logs', [ErrorController::class, 'renderLogs'])->name('renderLogs');
