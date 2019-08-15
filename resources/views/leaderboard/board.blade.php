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
                'rank' => $preview
                        ? $leaderboard->getCountryRank()->getPreviewRank()
                        : $leaderboard->getCountryRank(),
                'isGlobal' => false,
            ])

            @include('leaderboard.card', [
                'rank' => $preview
                        ? $leaderboard->getGlobalRank()->getPreviewRank()
                        : $leaderboard->getGlobalRank(),
                'isGlobal' => true,
            ])
        </div>
    </div>
</div>