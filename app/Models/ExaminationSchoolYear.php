<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExaminationSchoolYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'examination_school_year';
    protected $fillable = [
        'examination_id',
        'school_year_id',
    ];

    public function examinations()
    {
        return $this->belongsTo(Examination::class, 'examination_id');
    }

    public function schoolYears()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    public function students()
    {
        return $this->hasMany(StudentInformation::class, 'exam_school_year_id');
    }

}
