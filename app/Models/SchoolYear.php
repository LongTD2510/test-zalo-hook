<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolYear extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'year',
    ];

    public function examinations()
    {
        return $this->belongsToMany(Examination::class, 'examination_school_year', 'school_year_id', 'examination_id');
    }
}
