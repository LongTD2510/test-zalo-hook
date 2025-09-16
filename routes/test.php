<?php

use App\Http\Controllers\StudentInformationController;
use App\Models\SchoolYear;
use App\Models\StudentInformation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

Route::post('test-mail', [\App\Http\Controllers\JobController::class, 'sendRegisterMail']);
