<?php

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Enums\DateRangeEnum;

if (!function_exists('getTimestampAtMicro')) {
    function getTimestampAtMicro()
    {
        return Arr::get(gettimeofday(), 'sec') ?? microtime();
    }
}

if (!function_exists('generateUniqueName')) {
    function generateUniqueName($key)
    {
        $timestamp = now()->format('Uu');
        $md5 = md5($key . substr($timestamp, -1));
        $unique = substr($md5, 5, 6);
        $timestampPart = substr($timestamp, -4);

        return $unique . $timestampPart;
    }
}

if (!function_exists('getDateWithFormat')) {
    function getDateWithFormat($date, $format = 'd/m/Y')
    {
        return Carbon::parse($date)->format($format);
    }
}

if (!function_exists('formatDate')) {
    function formatDate(Carbon $date, $format = 'd/m/Y')
    {
        return $date->format($format);
    }
}

if (!function_exists('getIpAddress')) {
    function getIpAddress(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $remoteConnection =  $_SERVER['HTTP_CLIENT_IP'];
        }
        //if user is from the proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remoteConnection =  $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //if user is from the remote address
        else {
            $remoteConnection = $_SERVER['REMOTE_ADDR'];
        }

        return $remoteConnection;
    }
}

if (!function_exists('currentUser')) {
    function currentUser(bool $withCustomerInfo = false): array
    {
        $user = auth()->user();

        if (!$user instanceof \App\Models\User) {
            return [];
        }

        $userArray = $user->only([
            'id',
            'name',
            'email',
            'email_verified_at',
            'role',
        ]);

        $customer_info = $user->customer ? $user->customer->toArray() : [];

        if ($withCustomerInfo) {
            return [
                'user' => $userArray,
                'customer_info' => $customer_info ?? []
            ];
        }

        return [
            'user' => $userArray
        ];
    }
}

if (!function_exists('createActivityLog')) {
    function createActivityLog(
        $action,
        $type,
        $referenceId = null,
        $ip = 'unknown',
        $endpoint = 'unknown',
        $device = null,
        $browser = null,
    ): bool {
        try {
            $current_user = currentUser();
            $userId = Arr::get($current_user, 'user.id');

            $data = [
                'action' => $action,
                'action_by' => $userId,
                'type' => $type,
                'reference_id' => $referenceId,
                'ip' => $ip,
                'endpoint' => $endpoint,
                'device' => $device,
                'browser' => $browser,
            ];

            if (!$userId) {
                $data['action_by'] = 'unknown';
                ActivityLog::query()->create($data);
                return true;
            }

            ActivityLog::query()->create($data);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}

if (!function_exists('generateUniqueToken')) {
    function generateUniqueToken(): string
    {
        return substr(md5(Carbon::now()->timestamp . Str::random(8)), 0, 32);
    }
}


if (!function_exists('getStartEndInDateRange')) {
    function getStartEndInDateRange($dateRange)
    {
        $startDate = null;
        $endDate = null;
        $now = Carbon::now();
        switch ($dateRange) {
            case DateRangeEnum::LAST_30_DAYS:
                $endDate = $now->copy()->subDay();
                $startDate = $now->copy()->subDay()->subDays(30);
                break;
            case DateRangeEnum::LAST_7_DAYS:
                $endDate = $now->copy()->subDay();
                $startDate = $now->copy()->subDay()->subDays(7);
                break;
            case DateRangeEnum::LAST_MONTH:
                $endDate = $now->copy()->subMonthNoOverflow()->lastOfMonth();
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                break;
            case DateRangeEnum::THIS_MONTH:
                $endDate = $now->copy()->lastOfMonth();
                $startDate = $now->copy()->startOfMonth();
                break;
            case DateRangeEnum::LAST_QUARTER:
                $endDate = $now->copy()->subQuarterNoOverflow()->lastOfQuarter();
                $startDate = $now->copy()->subQuarterNoOverflow()->startOfQuarter();
                break;
            case DateRangeEnum::THIS_QUARTER:
                $endDate = $now->copy()->lastOfQuarter();
                $startDate = $now->copy()->startOfQuarter();
                break;
        }

        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }
}

if (!function_exists('getOrderNumberString')) {
    function getOrderNumberString($orderNumber)
    {
        $prefix = env('ORDER_NUMBER_PREFIX', 'OID');
        return "$prefix-$orderNumber";
    }
}

if (!function_exists('removeUnwantedStyles')) {
    function removeUnwantedStyles($html)
    {
        // Loại bỏ font-size, font-family, line-height trong thẻ style
        $patterns = [
            '/font-size:\s[^;"]+;?/i',    // Xóa font-size
            '/font-family:\s[^;"]+;?/i',  // Xóa font-family
            '/line-height:\s[^;"]+;?/i',   // Xóa line-height
            '/\sstyle\s=\s"[^"]\bfont:\s[^"]*"/i', // Xóa style font.
            '/\s*id\s*=\s*"[^"]*"/i',
        ];
        return preg_replace($patterns, '', $html);
    }
}

if (!function_exists('getFirstImageSrc')) {
    function getFirstImageSrc($html)
    {
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        $tags = $doc->getElementsByTagName('img');
        if ($tags->length > 0) {
            return $tags->item(0)->getAttribute('src');
        }

        return null;
    }
}

if (!function_exists('renderTemplateContent')) {
    function renderTemplateContent($content, array $params = [])
    {
        foreach ($params as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }
}