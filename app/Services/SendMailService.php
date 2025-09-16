<?php

namespace App\Services;

use App\Jobs\SendRegisterEmail;

class SendMailService
{

    public static function sendRegisterMail() : void
    {
        $emailJob = new SendRegisterEmail();
        dispatch($emailJob);
    }

}
