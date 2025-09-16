<?php

namespace App\Models;

use App\Enums\FileType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'description',
        'details',
        'status',
        'posted_date',
        'thumb_url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($posts) {
            if (empty($posts->slug)) {
                $baseSlug = Str::slug($posts->title);
                $slug = $baseSlug;
                $count = 1;

                while (self::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $posts->slug = $slug;
            }
        });
    }

    protected $casts = [
        'details' => 'array', // Tự động cast JSON thành mảng
        'posted_date' => 'datetime',
    ];

    /**
     * Quan hệ many-to-many với Category
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post', 'post_id', 'category_id')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'type_id')->where('type', FileType::POST);
    }
}