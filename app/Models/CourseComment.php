<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseComment extends Model
{
    protected $table = 'course_comments';

    protected $fillable = ['user_id', 'course_id', 'content'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select('id', 'name');
    }
}
