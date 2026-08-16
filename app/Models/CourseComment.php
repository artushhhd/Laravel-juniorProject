<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseComment extends Model
{
    protected $table = 'course_comments';
    protected $fillable = ['user_id', 'course_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'name');
    }
}
