<?php

namespace App\Models;

use App\Enums\FileType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $table = 'templates';

    protected $fillable = [
        'code',
        'channel',
        'user_id',
        'description',
        'thumb_url',
        'template_id',
        'name',
        'status',
        'template_quality',
        'content',
        'preview_url',
        'template_tag',
        'price',
        'params',
        'is_welcome',
        'is_active',
    ];

    protected $casts = [
        'params'      => 'array',
        'is_welcome'  => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'type_id')->where('type', FileType::TEMPLATE);
    }
}
