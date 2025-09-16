<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam',
    ];

    public function schoolYears()
    {
        return $this->belongsToMany(SchoolYear::class, 'examination_school_year', 'examination_id', 'school_year_id');
    }


}
