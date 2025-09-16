<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;
    protected $table = 'grade';
    protected $fillable = [
        'class_code',
        'class_name',
        'description',
        'schedule',
        'group_grade'
    ];
}
