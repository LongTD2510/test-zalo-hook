<?php

namespace App\Models;

use App\Enums\FileType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'files';

    protected $fillable = [
        'file_url',
        'type',
        'type_id',
        'sync_status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'type_id')->where('type', FileType::CATEGORY);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'type_id')->where('type', FileType::POST);
    }





}
