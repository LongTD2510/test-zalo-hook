<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'month_quarter_id',
        'grade_id',
        'name',
        'contact',
        'contact_2',
        'content'
    ];

    public function monthQuarter () {
        return $this->belongsTo(NotifyMonthQuarter::class, 'month_quarter_id', 'id');
    }

    public function grade () {
        return $this->belongsTo(Grade::class, 'grade_id', 'id');
    }
}
