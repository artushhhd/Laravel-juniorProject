<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || $user->isSuperAdmin();
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || $user->isSuperAdmin();
    }
}
