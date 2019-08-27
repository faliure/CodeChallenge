<?php

namespace App;

use App\Interfaces\Rank;

final class RankSlicer
{
    public static function process(Rank $rank): array
    {
        return ($rank->itemsCount() <= 9)
            ? $rank->items()
            : static::aggregateSlices($rank);
    }

    private static function aggregateSlices(Rank $rank)
    {
        $sliced = static::topSlice($rank)
                + static::bottomSlice($rank)
                + static::userSlice($rank)
                + (static::needsMiddleSlice($rank) ? static::middleSlice($rank) : []);

        ksort($sliced);

        return $sliced;
    }

    private static function topSlice(Rank $rank)
    {
        return array_slice($rank->items(), 0, 3, true);
    }

    private static function userSlice(Rank $rank)
    {
        $offset = max(0, $rank->userPosition() - 2);

        return array_slice($rank->items(), $offset, 3, true);
    }

    private static function middleSlice(Rank $rank): array
    {
        $userPosition = $rank->userPosition();

        $offset = floor(count($rank->items()) / 2) - 1;
        $length = min(3, 4 - min($userPosition - 1, $rank->itemsCount() - $userPosition));

        return array_slice($rank->items(), $offset, $length, true);
    }

    private static function bottomSlice(Rank $rank)
    {
        return array_slice($rank->items(), -3, 3, true);
    }

    /**
     * The sliced ranks need to show 9 students. When the current user's slice
     * overlaps with the top or bottom slices, we show an extra slice in the
     * middle of the rank, for reference.
     */
    private static function needsMiddleSlice(Rank $rank)
    {
        $userPosition = $rank->userPosition();

        return ($userPosition < 5 || $userPosition > $rank->itemsCount() - 4);
    }
}
