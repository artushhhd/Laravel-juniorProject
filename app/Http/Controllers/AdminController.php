<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $query = Course::with('author:id,name,role')
            ->withCount('likes')
            ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $user->id)]);

        if ($user->isModerator() && !$user->isAdmin()) {
            $query->whereHas('author', fn($q) => $q->whereNotIn('role', ['superadmin', 'admin']));
        }

        $courses = $query->latest()->paginate(30);

        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function updateCourse(Request $request, Course $course): JsonResponse
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Forbidden');

        $request->validate(['title' => 'required|string|max:255']);

        $course->update(['title' => $request->input('title')]);

        return response()->json(['success' => true, 'course' => $course]);
    }

    public function destroyCourse(Course $course): JsonResponse
    {
        $currentUser = Auth::user();
        $course->load('author:id,role');

        if ($course->author?->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json(['success' => true]);
    }

    public function approve(Course $course): JsonResponse
    {
        $course->update(['status' => 'published']);

        return response()->json(['success' => true, 'course' => $course]);
    }

    public function users(): JsonResponse
    {
        abort_if(Auth::user()->isModerator() && !Auth::user()->isAdmin(), 403);

        $users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->latest()
            ->paginate(30);

        return response()->json(['success' => true, 'users' => $users]);
    }

    public function toggleBlock(User $user): JsonResponse
    {
        $currentUser = Auth::user();
        abort_if(Auth::user()->isModerator() && !Auth::user()->isAdmin(), 403);

        if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['message' => 'Cannot manage admins'], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
        ]);
    }

    public function destroyUser(User $user): JsonResponse
    {
        $currentUser = Auth::user();
        abort_if(Auth::user()->isModerator() && !Auth::user()->isAdmin(), 403);

        if ($user->isSuperAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['message' => 'Cannot delete admins'], 403);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
