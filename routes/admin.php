<?php

use App\Enums\FileType;
use App\Enums\FileTypeEnum;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ZaloController;
use App\Jobs\DownloadImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| ADMIN API Routes
|--------------------------------------------------------------------------
*/

Route::get('/test-upload-r2', function () {
    //testing upload r2
    $thumbnail = 'https://r2.nguoiviettainga.ru/1755271883.39.webp';
    DownloadImage::dispatch([$thumbnail], 999, FileType::HOME_PAGE, 'r2')->onQueue('image');
    return response()->json(['message' => 'Image download job dispatched successfully.']);
});

//Route::get('/test-log-to-discord', function () {
//    // Testing logging to Discord
//    logToDiscord("TEST LOG DISCORD");
//    return 1;
//});



Route::prefix('homepage-config')->name('homepage.')->group(function () {
    Route::any('/list', [HomePageController::class, 'listingConfig']);
    Route::post('/store', [HomePageController::class, 'store']);
    Route::put('/update/{key}', [HomePageController::class, 'update']);
    Route::delete('/delete/{key}', [HomePageController::class, 'destroy']);
    Route::get('/get-config/{key}', [HomePageController::class, 'getConfigByKey']);
    Route::post('/restore-default/{key}', [HomePageController::class, 'restoreDefaultConfig']);
    Route::post('/upload-image', [HomePageController::class, 'uploadImage']);
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::any('/list', [CategoryController::class, 'listingCategories']);
    Route::post('/store', [CategoryController::class, 'store']);
    Route::put('/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
    Route::post('/bulk-store', [CategoryController::class, 'bulkStore']);
});

Route::prefix('posts')->name('posts.')->group(function () {
    Route::any('/list', [PostController::class, 'listingPosts']);
    Route::post('/store', [PostController::class, 'store']);
    Route::put('/update/{id}', [PostController::class, 'update']);
    Route::delete('/delete', [PostController::class, 'destroy']);
    Route::post('/upload-image', [PostController::class, 'uploadImage']);
});

Route::prefix('zalo-notfication')->name('zalo-notfication.')->group(function () {
    Route::any('/list-template-zns', [ZaloController::class, 'getTemplates']);
    Route::get('/template-zns/get/{id}', [ZaloController::class, 'getInfo']);
    Route::post('/send', [ZaloController::class, 'send']);
    Route::post('/save-token', [ZaloController::class, 'saveToken']);
    Route::get('/token', [ZaloController::class, 'getToken']);
    Route::post('/zalo/webhook', [ZaloController::class, 'handle']);
});

Route::prefix('template')->name('template.')->group(function () {
    Route::any('/list', [TemplateController::class, 'listingTemplate']);
    Route::post('/store', [TemplateController::class, 'store']);
    Route::put('/update/{id}', [TemplateController::class, 'update']);
    Route::delete('/delete', [TemplateController::class, 'destroy']);
    Route::post('/upload-image', [TemplateController::class, 'uploadImage']);
});
