<?php

namespace App\Http\Controllers;

use App\Services\TeacherService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use ApiResponse;
    public function testDiscordLog() {
        logToDiscord("TEST");
    }

}
