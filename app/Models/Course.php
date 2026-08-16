<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'published_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function likes()
{
    return $this->belongsToMany(User::class, 'course_likes');
}

public function isLikedBy(?User $user): bool
{
    if (!$user) return false;
    return $this->likes()->where('user_id', $user->id)->exists();
}
public function comments()
{
    return $this->hasMany(CourseComment::class)->with('user')->latest();
}
}
