<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Course;
use App\CourseEnrollment;
use App\Leaderboard;
use Illuminate\Http\Response;
use Illuminate\Contracts\Support\Renderable;

class CourseEnrollmentController extends Controller
{
    public function show(string $slug, Leaderboard $leaderboard) : Renderable
    {
        /** @var Course $course */
        $course = Course::query()
                ->where('slug', $slug)
                ->first() ?? abort(Response::HTTP_NOT_FOUND, 'Course not found');

        $enrollment = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->with('course.lessons')
            ->first();

        if ($enrollment === null) {
            return view('courses.show', compact('course'));
        }

        return view('courseEnrollments.show', [
            'enrollment'      => $enrollment,
            'leaderboardCard' => $leaderboard->board($course->id),
        ]);
    }

    public function store(string $slug)
    {
        /** @var Course $course */
        $course = Course::query()
                ->where('slug', $slug)
                ->first() ?? abort(Response::HTTP_NOT_FOUND, 'Course not found');

        $course->enroll(auth()->user());

        return redirect()->action([self::class, 'show'], [$course->slug]);
    }
}
