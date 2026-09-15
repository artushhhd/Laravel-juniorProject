<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Course;
use App\Models\CourseComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        $courses = Course::with(['author:id,name', 'comments.user'])
            ->withCount('likes')
            ->when($user, fn($q) => $q->withExists([
                'likes as is_liked' => fn($q) => $q->where('user_id', $user->id),
            ]))
            ->latest()
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $courses]);
    }

    public function show(Course $course): JsonResponse
    {
        $course->load(['author:id,name', 'comments.user']);

        return response()->json(['success' => true, 'data' => $course]);
    }

    public function store(CourseRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $course = Course::create($data);

        return response()->json(['success' => true, 'data' => $course], 201);
    }

    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        Gate::authorize('update', $course);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $course->update($data);

        return response()->json(['success' => true, 'data' => $course]);
    }

    public function destroy(Course $course): JsonResponse
    {
        Gate::authorize('delete', $course);

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json(['success' => true]);
    }

    public function toggleLike(Course $course): JsonResponse
    {
        $user = Auth::user();
        $result = $course->likes()->toggle($user->id);

        return response()->json([
            'success' => true,
            'is_liked' => count($result['attached']) > 0,
            'likes_count' => $course->likes()->count(),
        ]);
    }

    public function storeComment(StoreCommentRequest $request, Course $course): JsonResponse
    {
        $comment = CourseComment::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'content' => $request->validated('content'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $comment->load('user'),
        ], 201);
    }
}
