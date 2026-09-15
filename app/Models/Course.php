<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'image',
        'price',
        'status',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_likes');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CourseComment::class)->latest();
    }
}
