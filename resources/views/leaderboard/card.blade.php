<?php
/**
 * @var array  $slicedScores  Array containing 'country' and 'global' lists of rankItems
 * @var int    $countryRank   Current user's rank in the user's country
 * @var int    $globalRank    Current user's rank globally
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
            <div class="col-md-6">
                @include('leaderboard.sliced', [
                    'title'  => "You are ranked <b>$countryRank</b> in " . auth()->user()->country->name,
                    'scores' => $slicedScores['country']
                ])
            </div>

            <div class="col-md-6">
                @include('leaderboard.sliced', [
                    'title'  => "You are ranked <b>$globalRank</b> Worldwide",
                    'scores' => $slicedScores['global']
                ])
            </div>
        </div>
    </div>
</div>