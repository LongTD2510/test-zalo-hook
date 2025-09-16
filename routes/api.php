<?php

use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\ExaminationSchoolYearController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\StudentInformationController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\NotifyController;
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

// Route for retrieving authenticated user details
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth.access.api')
        ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::post('/change-pass', [AuthController::class, 'changePassWord']);
    });
});

// Examination School Year management
Route::middleware('auth.access.api')
    ->prefix('examination-school-year')
    ->group(function () {
    Route::get('/list', [ExaminationSchoolYearController::class, 'index']);
    Route::post('/store', [ExaminationSchoolYearController::class, 'store']);
    Route::post('/store-examination-school-years', [ExaminationSchoolYearController::class, 'storeExaminationSchoolYears']);
    Route::post('/update', [ExaminationSchoolYearController::class, 'update']);
    Route::post('/delete', [ExaminationSchoolYearController::class, 'destroy']);
});

// Examination management
Route::prefix('examination')
    ->group(function () {
        Route::get('/list', [ExaminationController::class, 'index']);
    });
Route::middleware('auth.access.api')
    ->prefix('examination')
    ->group(function () {
    Route::post('/store', [ExaminationController::class, 'store']);
    Route::post('/store-examinations', [ExaminationController::class, 'storeExaminations']);
    Route::post('/update', [ExaminationController::class, 'update']);
    Route::post('/delete', [ExaminationController::class, 'destroy']);
});

// School Year management
Route::prefix('school-year')
    ->group(function () {
    Route::get('/list', [SchoolYearController::class, 'getList']);
});
Route::middleware('auth.access.api')
    ->prefix('school-year')
    ->group(function () {
    Route::post('/store', [SchoolYearController::class, 'store']);
    Route::post('/store-school-years', [SchoolYearController::class, 'storeSchoolYears']);
    Route::post('/update', [SchoolYearController::class, 'update']);
    Route::post('/delete', [SchoolYearController::class, 'destroy']);
});

// Student Information management
Route::prefix('student-information')
    ->group(function () {
    Route::get('/get-student', [StudentInformationController::class, 'getStudent']);
});
Route::middleware('auth.access.api')
    ->prefix('student-information')
    ->group(function () {
    Route::get('/list', [StudentInformationController::class, 'getList']);
    Route::post('/store', [StudentInformationController::class, 'store']);
    Route::post('/store-students', [StudentInformationController::class, 'storeStudents']);
    Route::post('/update', [StudentInformationController::class, 'update']);
    Route::post('/bulk-update', [StudentInformationController::class, 'bulkUpdate']);
    Route::post('/delete', [StudentInformationController::class, 'destroy']);
});

// Teacher Information management
Route::prefix('teachers')
    ->group(function () {
        Route::get('/list', [TeacherController::class, 'getTeachers']);
        Route::get('/{id}', [TeacherController::class, 'getTeacherById']);
    });

// Additional Result management route
Route::middleware('auth.access.api')
    ->prefix('result')
    ->group(function () {
    Route::get('/manager', [StudentInformationController::class, 'index']); // Note: Check if correct controller/method
});

Route::middleware('auth.access.api')
    ->prefix('notify')
    ->group(function () {
    Route::get('/list', [NotifyController::class, 'index']);
    Route::post('/store', [NotifyController::class, 'store']);
});

Route::group(['prefix' => 'public'], function () {
    Route::get('list-config-homepage', [HomePageController::class, 'listingConfig']);
    Route::get('years-exams-info', [StudentInformationController::class, 'getExamsYearsFilter']);
    Route::get('months-quarter', [StudentInformationController::class, 'monthQuarter']);
    Route::get('notify', [NotifyController::class, 'detail']);
    Route::get('map-school-years-with-exam', [StudentInformationController::class, 'mapSchoolYearsWithExam']);
    Route::get('get-litsing-news', [ListingController::class, 'getNewsList']);
    Route::get('get-litsing-video', [ListingController::class, 'getVideoList']);
    Route::get('get-post-pagination/{postId}', [ListingController::class, 'getPostPagination']);
    Route::get('get-grade-detail/{gradeCode}', [ListingController::class, 'getGradeDetail']);
    Route::get('get-grades-list/{gradeCode}', [ListingController::class, 'getGradesListRecommend']);
    Route::get('grades', [ListingController::class, 'getGrades']);
    Route::post('test-mail', [\App\Http\Controllers\JobController::class, 'sendRegisterMail']);
    Route::get('test', [ListingController::class, 'test']);
});

Route::get('/test-cors', function () {
    return response()->json(['message' => 'CORS is working']);
});

Route::get('/test-discord-log', [TestController::class, 'testDiscordLog']);

