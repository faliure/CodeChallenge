<?php declare(strict_types=1);

namespace App;

use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 */
final class Leaderboard
{
    /**
     * @var ?int
     */
    private $courseId;

    /**
     * @var array
     */
    private $scores;


    /**
     *
     */
    public function __construct(?int $courseId)
    {
        $this->courseId = $courseId;

        $this->scores = $this->fetchScores();
    }

    /**
     *
     */
    public function getGlobalRank(bool $previewOnly = true)
    {
        $rank = new Rank($this->scores);

        return $previewOnly ? $rank->getPreviewRank() : $rank;
    }

    /**
     *
     */
    public function getCountryRank(?int $countryId = null, bool $previewOnly = true)
    {
        $rank = (new Rank($this->scores))->getCountryRank($countryId);

        return $previewOnly ? $rank->getPreviewRank() : $rank;
    }

    /**
     *
     */
    private function fetchScores()
    {
        $query = DB::table('quiz_answers AS qa')
            ->select('qa.user_id', DB::Raw('u.name AS user_name'), 'u.country_id', DB::raw('SUM(score) AS score'))
            ->join('quizzes AS q', 'q.id', '=', 'qa.quiz_id')
            ->join('lessons AS l', 'l.id', '=', 'q.lesson_id')
            ->join('users AS u', 'u.id', '=', 'qa.user_id')
            ->groupBy('u.id')
            ->orderBy('score', 'DESC')
            ->orderBy(DB::raw('IF(u.id = ' . auth()->user()->id . ', 0, 1)'));

        if ($this->courseId) {
            $query->where('l.course_id', '=', $this->courseId);
        }

        return $query->get()->all();
    }
}
