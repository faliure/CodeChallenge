<?php declare(strict_types=1);

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $name
 */
final class Leaderboard
{
    public function board(int $courseId)
    {
        $scores = $this->scores($courseId);
        $slicedScores = $this->scoresSlices($scores);

        $globalRank = $this->userRank($slicedScores['global'], Auth::user()->id);
        $countryRank = $this->userRank($slicedScores['country'], Auth::user()->id);

        return view('leaderboard.show', compact('slicedScores', 'globalRank', 'countryRank'));
    }

    private function userRank(array $scores, int $userId)
    {
        $userRankItem = array_column($scores, null, 'user_id')[$userId];

        return array_search($userRankItem, $scores) + 1;

        /**
         * Alternative algo - TODO : evaluate which one is better
         */
        // foreach ($scores as $zeroBasedRank => $rankItem) {
        //     if ($rankItem->user_id === $userId) {
        //         return $zeroBasedRank + 1;
        //     }
        // }
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
    private function slicedScores($scores) : array
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
