<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyMonthQuarter extends Model
{
    use HasFactory;
    protected $table = 'notify_month_quarter';
    protected $fillable = [
        'name',
        'slug',
        'week',
        'month',
        'year',
    ];

    public function notifications () {
        return $this->hasMany(Notification::class, 'month_quarter_id', 'id');
    }
}
