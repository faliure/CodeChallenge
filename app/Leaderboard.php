<?php declare(strict_types=1);

namespace App;

use App\Interfaces\Rank;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 */
final class Leaderboard
{
    /**
     * @var int
     */
    private $courseId;

    /**
     * Limit Leaderboard to a single course.
     */
    public function forCourse(?int $courseId)
    {
        $this->courseId = $courseId;

        return $this;
    }

    /**
     * Get a Rank object for the global rank of the current course.
     */
    public function globalRank(): Rank
    {
        return resolve(Rank::class, $this->scores());
    }

    /**
     * Get the rank for a given country, by id.
     */
    public function countryRank(int $countryId): Rank
    {
        return resolve(Rank::class, $this->scores())->forCountry($countryId);
    }

    /**
     * Get scores from cache or fetch from database if not found in cache.
     */
    public function scores(): array
    {
        $cacheKey = static::scoresCacheKey($this->courseId);

        return Cache::remember($cacheKey, 300, function() {
            return $this->fetchScores();
        });
    }

    /**
     * Fetch an ordered list of student scores for a given course.
     */
    private function fetchScores(): array
    {
        $query = DB::table('quiz_answers AS qa')
            ->select('qa.user_id', DB::Raw('u.name AS user_name'), 'u.country_id', DB::raw('SUM(score) AS score'))
            ->join('quizzes AS q', 'q.id', '=', 'qa.quiz_id')
            ->join('lessons AS l', 'l.id', '=', 'q.lesson_id')
            ->join('users AS u', 'u.id', '=', 'qa.user_id')
            ->groupBy('u.id')
            ->orderBy('score', 'DESC')
            ->orderBy(DB::raw('IF(u.id = ' . auth()->id() . ', 0, 1)'));

        if ($this->courseId) {
            $query->where('l.course_id', '=', $this->courseId);
        }

        return $query->get()->toArray();
    }

    /**
     * Invalidate scores cache for a given course, and invalidate global scores.
     */
    public static function forgetCachedScores(int $courseId): void
    {
        Cache::forget(static::scoresCacheKey($courseId));
        Cache::forget(static::scoresCacheKey(null));
    }

    /**
     * Get cache key for course scores or global scores (courseId = null).
     */
    private static function scoresCacheKey(?int $courseId): string
    {
        return 'leaderboard:scores:' . ($courseId ?? 'global');
    }
}
