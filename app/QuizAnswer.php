<?php declare(strict_types=1);

namespace App;

use App\Events\QuizAnswerEvaluated;
use App\Events\QuizAnswerEvaluating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $quiz_id
 * @property int $user_id
 * @property string $answer
 * @property string $score
 *
 * @property Quiz $quiz
 */
final class QuizAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'answer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function grade(int $score, GraderInterface $gradedBy): void
    {
        $maxScore = $this->quiz->max_score;

        if ($score > $maxScore) {
            throw new \OutOfBoundsException("Score cannot be higher than the maximum for this quiz (max={$maxScore})");
        }

        event(new QuizAnswerEvaluating($this, $score, $gradedBy));

        $this->score = $score;
        $this->save();

        event(new QuizAnswerEvaluated($this, $score, $gradedBy));
    }
}
