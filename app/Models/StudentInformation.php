<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_information';
    protected $fillable = [
        'student_id',
        'full_name',
        'exam_school_year_id',
        'room',
        'location',
        'math',
        'english',
        'literature',
        'birth_date',
        'link_exam',
        'contact',
        'external_id',
        'contact2',
        'time',
        'subject'
    ];

    public function examinationSchoolYear()
    {
        return $this->belongsTo(ExaminationSchoolYear::class, 'exam_school_year_id', 'id');
    }

}
