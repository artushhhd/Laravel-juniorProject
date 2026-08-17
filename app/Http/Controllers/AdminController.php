<?php

namespace App\Http\Controllers;

use App\Models\{User, Course};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, Auth};
class AdminController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $query = Course::with('author')->withCount('likes');

        if ($user && str_contains(strtolower($user->role), 'moder')) {
            $query->whereHas('author', function ($q) {
                $q->whereNotIn('role', ['superadmin', 'admin']);
            });
        }

        $courses = $query->latest()->get()->map(function ($course) use ($user) {
            $course->is_liked = $user ? $course->likes()->where('user_id', $user->id)->exists() : false;
            return $course;
        });

        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function updateCourse(Request $request, int $id): JsonResponse
    {
        $currentUser = Auth::user();

        if (!$currentUser || strtolower($currentUser->role) !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $course = Course::findOrFail($id);
        $course->title = $request->input('title');
        $course->save();

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'course'  => $course
        ]);
    }

    public function destroyCourse(int $id): JsonResponse
    {
        $course = Course::with('author')->findOrFail($id);
        $currentUser = Auth::user();

        if ($course->author && strtolower($course->author->role) === 'superadmin') {
            if (strtolower($currentUser->role) !== 'superadmin') {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
        }

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    public function users(): JsonResponse
    {
        $this->checkModer();
        return response()->json([
            'success' => true,
            'users' => User::where('id', '!=', Auth::id())->latest()->get()
        ]);
    }

    public function toggleBlock(int $id): JsonResponse
    {
        $this->checkModer();

        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        $targetRole = strtolower($user->role);
        $currentRole = strtolower($currentUser->role);

        if ($targetRole === 'superadmin' && $currentRole !== 'superadmin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($targetRole === 'admin' && $currentRole === 'admin') {
            return response()->json(['message' => 'Admins cannot block other admins'], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool)$user->is_active,
            'message' => $user->is_active ? 'Unblocked' : 'Blocked'
        ]);
    }

    public function destroyUser(int $id): JsonResponse
    {
        $this->checkModer();

        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        $targetRole = strtolower($user->role);
        $currentRole = strtolower($currentUser->role);

        if ($targetRole === 'superadmin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($targetRole === 'admin' && $currentRole === 'admin') {
            return response()->json(['message' => 'Admins cannot delete other admins'], 403);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    private function checkModer(): void
    {
        if (str_contains(strtolower(Auth::user()->role), 'moder')) {
            abort(response()->json(['message' => 'Forbidden for moderators'], 403));
        }
    }
}
