<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

if (!function_exists('logToDiscord')) {
    function logToDiscord($message, string $channel = 'error')
    {
        $date = Carbon::now()->toDateTimeString();
        $messageWarningPrefix = '';
        switch ($channel) {
            case ('error'):
                $messageWarningPrefix = '⛔ Exception Logs ';
                break;
            case ('confirm_order'):
                $messageWarningPrefix = '🔰 Confirmed order Logs ';
                break;
            case ('new_order'):
                $messageWarningPrefix = '🔰 New order Logs ';
                break;
            case ('release_ticket'):
                $messageWarningPrefix = '🔰 Release ticket Logs ';
                break;
            case ('reject_order'):
                $messageWarningPrefix = '⛔ Reject order Logs ';
                $channel = 'confirm_order';
                break;
            case ('seat_order'):
                $messageWarningPrefix = '⛔ SEAT Error Logs ';
                break;
            default:
                $messageWarningPrefix = '⛔ Exception Logs ';
                break;
        }
        $env = app()->environment();
        $messageLog =  "$messageWarningPrefix  - Time: {$date} \r\n\r\n" .
            "🔰 environment: {$env} \n" .
            "🔰 Message: {$message}" .
            "\r\n";
        $vailableLogChannels = array_keys(config('logging.channels.discord'));
        $logChannel = '';
        if (!in_array($channel, $vailableLogChannels)) {
            $logChannel = "discord.error";
        } else {
            $logChannel = "discord.$channel";
        }
        Log::channel($logChannel)->info($messageLog);
    }
}

if (!function_exists('logException')) {
    function logException(Throwable|Exception $ex, $name = __FUNCTION__, $channel = 'error', $debug = false)
    {
        $env = app()->environment();
        $date = Carbon::now()->toDateTimeString();
        $messageLog =   "⛔ Exception Logs - Time: {$date} \r\n\r\n" .
            "🔰 environment: {$env}" .
            "🔰 Type: " . get_class($ex) . "\r\n" .
            "🔰 Function: " . $name . "\r\n" .
            "🔰 File: {$ex->getFile()}:{$ex->getLine()}" . "\r\n" .
            "🔰 Message: {$ex->getMessage()}" .
            "\r\n";
        Log::channel('discord.error')->info($messageLog);
    }
}
