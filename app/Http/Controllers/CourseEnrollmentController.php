<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Course;
use App\CourseEnrollment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;

class CourseEnrollmentController extends Controller
{
    public function show(string $slug) : Renderable
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

        $scores = $this->scores($course->id);
        $slicedScores = $this->scoresSlices($scores);

        return view('courseEnrollments.show', compact('enrollment', 'slicedScores'));
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

    /**
     *
     */
    private function scoresSlices(array $globalScores) : array
    {
        $userCountryId = Auth::user()->country_id;

        $countryScores = array_values(
            array_filter($globalScores, function($score) use ($userCountryId) {
                return $score->country_id === $userCountryId;
            })
        );

        return [
            'global'  => $this->slicedScores($globalScores),
            'country' => $this->slicedScores($countryScores),
        ];
    }

    /**
     *
     */
    private function slicedScores($scores)
    {
        $currentUserRank = array_search(Auth::user()->id, array_column($scores, 'user_id'));

        return array_slice($scores, 0, 3, true)                     // First 3
             + array_slice($scores, $currentUserRank - 1, 3, true)  // User-centred 3
             + array_slice($scores, -3, 3, true);                   // Last 3
    }

    /**
     *
     */
    private function scores(int $courseId) : array
    {
        return DB::table('quiz_answers AS qa')
            ->join('quizzes AS q', 'q.id', '=', 'qa.quiz_id')
            ->join('lessons AS l', 'l.id', '=', 'q.lesson_id')
            ->join('users AS u', 'u.id', '=', 'qa.user_id')
            ->where('l.course_id', '=', $courseId)
            ->groupBy('u.id')
            ->orderBy('score', 'DESC')
            ->orderBy(DB::raw('IF(u.id = ' . Auth::user()->id . ', 0, 1)'))
            ->select('qa.user_id', DB::Raw('u.name AS user_name'), 'u.country_id', DB::raw('SUM(score) AS score'))
            ->get()
            ->toArray();
    }
}
