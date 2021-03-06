<?php declare(strict_types=1);

namespace App\Interfaces;

use App\RankItem;

interface Rank
{
    /**
     * $rankItems must be an array of stdClass objects with at least the
     * following properties: user_id, user_name, country_id, and position.
     */
    public function __construct(array $rankItems);

    /**
     * Fetch the list of rankItems, ordered by position ascending.
     */
    public function items(): array;

    /**
     * Given a rankItem, whether the next position's item exists.
     */
    public function hasNext(RankItem $item): bool;

    /**
     * Get the rank position of a user (defaults to current user).
     */
    public function userPosition(?int $userId = null): ?int;

    /**
     * Get the rank position of a user (defaults to current user), as an ordinal (e.g. "7th").
     */
    public function ordinalUserPosition(?int $userId = null): string;

    /**
     * Get the rankItem object for a user (defaults to current user).
     */
    public function userRankItem(?int $userId = null): RankItem;

    /**
     * Get a new Rank object, with rankItems filtered by country (defaults to current user's country).
     */
    public function forCountry(int $countryId): Rank;

    /**
     * Get a new Rank object, with a subset of the original rank (e.g. first 10)
     */
    public function summarize(): Rank;
}
