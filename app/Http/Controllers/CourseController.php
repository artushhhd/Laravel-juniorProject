<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\CourseComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{


   public function index(Request $request): JsonResponse
{
    $user = auth('sanctum')->user();

    $courses = Course::with(['author', 'comments.user'])
        ->withCount('likes')
        ->when($user, function ($query) use ($user) {
            $query->withExists(['likes as is_liked' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }]);
        }, function ($query) {
            $query->selectSub('0', 'is_liked');
        })
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'data' => $courses
    ]);
}
public function store(CourseRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $data['user_id'] = $request->user()->id;
        $course = Course::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data'    => $course
        ], 201);
    }

    public function show(Request $request, Course $course): JsonResponse
{
    $user = auth('sanctum')->user();

    $course->load(['author', 'comments.user']);
    $course->loadCount('likes');

    if ($user) {
        $course->is_liked = $course->likes()->where('user_id', $user->id)->exists();
    } else {
        $course->is_liked = false;
    }

    return response()->json([
        'success' => true,
        'message' => 'Course retrieved successfully',
        'data'    => $course
    ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data'    => $course
        ]);
    }

    public function destroy(Request $request, Course $course): JsonResponse{

    $user = $request->user();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated. Please login first.'
        ], 401);
    }
    if ($course->user_id !== $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. You can only delete your own courses.'
        ], 403);
    }

    if ($course->image) {
        Storage::disk('public')->delete($course->image);
    }

    $course->delete();

    return response()->json([
        'success' => true,
        'message' => 'Course deleted successfully',
        'data'    => null
    ]);
}

    public function toggleLike(int $id): JsonResponse
{
    $course = Course::findOrFail($id);
    $user = Auth::user();
    $status = $course->likes()->toggle($user->id);

    return response()->json([
        'success' => true,
        'is_liked' => count($status['attached']) > 0,
        'likes_count' => $course->likes()->count()
    ]);
}
public function storeComment(StoreCommentRequest $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $comment = CourseComment::create([
            'user_id'   => $request->user()->id,
            'course_id' => $course->id,
            'content'   => $request->validated()['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data'    => $comment->load('user')
        ], 201);
    }
}
