<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create(['name' => 'Admin']);

        $newCourses = [
            ['title' => 'Vue.js 3 Composition API', 'description' => 'Master the new reactive patterns in Vue 3.'],
            ['title' => 'Advanced Laravel Security', 'description' => 'Protect your applications from modern threats.'],
            ['title' => 'Python for AI & Machine Learning', 'description' => 'Build intelligent systems from scratch.'],
            ['title' => 'Mastering Tailwind CSS', 'description' => 'Create responsive designs at lightning speed.'],
            ['title' => 'PostgreSQL for Professionals', 'description' => 'Deep dive into database architecture.'],
            ['title' => 'Node.js Microservices', 'description' => 'Scale your backend with distributed systems.'],
            ['title' => 'React Performance Optimization', 'description' => 'Make your apps run at 60fps.'],
            ['title' => 'AWS Lambda & Serverless', 'description' => 'Run code without managing servers.'],
            ['title' => 'Digital Marketing Basics', 'description' => 'Grow your product presence online.'],
            ['title' => 'Figma for UI Designers', 'description' => 'Create stunning prototypes in Figma.'],
        ];

        foreach ($newCourses as $course) {
            Course::create([
                'user_id'     => $user->id,
                'title'       => $course['title'],
                'slug'        => Str::slug($course['title']),
                'description' => $course['description'],
                'price'       => rand(99, 499),
                'status'      => 'published',
                'published_at'=> now(),
                'image'       => 'https://picsum.photos/seed/' . Str::slug($course['title']) . '/640/480',
            ]);
        }
    }
}
