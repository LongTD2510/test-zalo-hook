<?php

namespace App\Http\Controllers;
use App\Jobs\SendRegisterEmail;
use App\Services\SendMailService;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Handle Queue Process
     */
    public function sendRegisterMail(Request $request)
    {
        SendMailService::sendRegisterMail();
        return 1;
    }
}
