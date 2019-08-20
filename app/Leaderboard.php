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
     * @var array
     */
    private $scores;

    public function __construct(?int $courseId)
    {
        $this->scores = $this->scores($courseId);
    }

    /**
     * Get a Rank object for the global rank of the current course.
     *
     * @param bool     $previewOnly If true, get a short version of the rank (e.g. slices)
     *
     * @return Rank    A new Rank object with a subset of the total positions
     */
    public function getGlobalRank(bool $previewOnly = true): Rank
    {
        $rank = resolve(Rank::class, $this->scores);

        return $previewOnly ? $rank->getPreviewRank() : $rank;
    }

    /**
     * Get the rank for a given country, by id.
     *
     * @param int|null $countryId   If omitted, use current user's country
     * @param bool     $previewOnly If true, get a short version of the rank (e.g. slices)
     *
     * @return Rank    A new Rank object with a subset of the total positions
     */
    public function getCountryRank(?int $countryId = null, bool $previewOnly = true): Rank
    {
        $rank = resolve(Rank::class, $this->scores)->getCountryRank($countryId);

        return $previewOnly ? $rank->getPreviewRank() : $rank;
    }

    /**
     * Invalidate scores cache for a given course.
     */
    public static function forgetCachedScores(int $courseId): void
    {
        Cache::forget(static::scoresCacheKey($courseId));
    }

    /**
     * Get scores from cache or fetch from database if not found in cache.
     */
    private function scores(int $courseId): array
    {
        $cacheKey = static::scoresCacheKey($courseId);

        return Cache::remember($cacheKey, 300, function() use ($courseId) {
            return $this->fetchScores($courseId);
        });
    }

    /**
     * Fetch an ordered list of student scores for a given course.
     */
    private function fetchScores(int $courseId): array
    {
        $query = DB::table('quiz_answers AS qa')
            ->select('qa.user_id', DB::Raw('u.name AS user_name'), 'u.country_id', DB::raw('SUM(score) AS score'))
            ->join('quizzes AS q', 'q.id', '=', 'qa.quiz_id')
            ->join('lessons AS l', 'l.id', '=', 'q.lesson_id')
            ->join('users AS u', 'u.id', '=', 'qa.user_id')
            ->where('l.course_id', '=', $courseId)
            ->groupBy('u.id')
            ->orderBy('score', 'DESC')
            ->orderBy(DB::raw('IF(u.id = ' . auth()->id() . ', 0, 1)'));

        return $query->get()->toArray();
    }

    private static function scoresCacheKey(int $courseId): string
    {
        return 'leaderboard:scores:' . $courseId;
    }
}
