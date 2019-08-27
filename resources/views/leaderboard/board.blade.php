<?php
/**
 * @var App\Leaderboard  $leaderboard
 * @var bool             $preview      Show only main slices of the Leaderboard (first, last, and middle slice)
 */
?>
<div class="card mt-4">
    <h2 class="card-header">Statistics</h2>
    <div class="card-body">
        <p>
            Your rankings improve every time you answer a question correctly.
            Keep learning and earning course points to become one of our top learners!
        </p>

        <div class="row">
            @include('leaderboard.card', [
                'rank' => $summary
                        ? $leaderboard->countryRank(auth()->user()->country_id)->summarize()
                        : $leaderboard->countryRank(auth()->user()->country_id),
            ])

            @include('leaderboard.card', [
                'rank' => $summary
                        ? $leaderboard->globalRank()->summarize()
                        : $leaderboard->globalRank(),
            ])
        </div>
    </div>
</div>