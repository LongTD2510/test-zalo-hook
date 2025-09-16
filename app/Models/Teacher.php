<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teachers';

    protected $fillable = [
        'name',
        'age',
        'bullet_point',
        'description',
        'motto',
        'quote',
        'viewpoint',
        'file_url'
    ];

    protected $casts = [
        'bullet_point' => 'array'
    ];

    public function getShortDescriptionAttribute()
    {
        return Str::limit($this->attributes['description'], 100, '...');
    }
}
