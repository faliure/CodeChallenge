<?php

namespace App\Listeners;

use App\Events\QuizAnswerEvaluated;
use App\Leaderboard;

class ClearLeaderboardCache
{
    public function handle(QuizAnswerEvaluated $event)
    {
        Leaderboard::forgetCachedScores($event->quizAnswer->quiz->lesson->course_id);
    }
}
