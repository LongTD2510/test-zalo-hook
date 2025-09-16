<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasTrackingId
{
    /**
     * Boot trait for HasTrackingId
     */
    protected static function bootHasTrackingId()
    {
        static::creating(function ($model) {
            if (empty($model->tracking_id)) {
                $model->tracking_id = self::generateTrackingId(
                    $model->tracking_prefix ?? class_basename($model)
                );
            }
        });
    }

    /**
     * Generate tracking_id for Zalo API
     */
    public static function generateTrackingId(string $prefix = 'ZNS'): string
    {
        $prefix = strtoupper(preg_replace('/[^A-Za-z0-9_]/', '', $prefix));
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(6));
        $trackingId = "{$prefix}_{$timestamp}_{$random}";
        return substr($trackingId, 0, 48);
    }
}
