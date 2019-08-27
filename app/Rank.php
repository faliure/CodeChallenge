<?php

namespace App;

use App\Interfaces\Rank as RankInterface;
use App\RankItem;
use App\RankSlicer;

final class Rank implements RankInterface
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @var bool
     */
    protected $isGlobal = true;

    /**
     *
     */
    public function __construct(array $items)
    {
        array_walk($items, [$this, 'registerRankItem']);
    }

    public function isGlobal()
    {
        return $this->isGlobal;
    }

    /**
     * Fetch the list of RankItems in the current Rank.
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Get the amount of RankItems in the current Rank.
     */
    public function itemsCount(): int
    {
        return count($this->items);
    }

    /**
     * Return whether the consecutive position after $item's exists in the current Rank.
     */
    public function hasNext(RankItem $item): bool
    {
        return isset($this->items[$item->position]);
    }

    /**
     * Get the position in the rank for a given user (currently logged in user if $userId is ommitted).
     */
    public function userPosition(?int $userId = null): ?int
    {
        $userId = $userId ?? auth()->id();

        return array_search($this->userRankItem($userId), $this->items) + 1 ?: null;
    }

    /**
     * Get the rank position of a user (defaults to current user), as an ordinal (e.g. "7th").
     */
    public function ordinalUserPosition(?int $userId = null): string
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        return $formatter->format($this->userPosition($userId));
    }

    /**
     * Get a new Rank as a subset with RankItems from a given country only.
     */
    public function forCountry(int $countryId): RankInterface
    {
        $countryItems = array_filter($this->items, function(RankItem $item) use ($countryId) {
            return $item->country_id === $countryId;
        });

        $countryRank = new static(array_values($countryItems));
        $countryRank->isGlobal = false;

        return $countryRank;
    }

    /**
     * Get the RankItem for a given user (currently logged in user if $userId is ommitted).
     */
    public function userRankItem(?int $userId = null): RankItem
    {
        $userId = $userId ?? auth()->id();

        foreach ($this->items as $item) {
            if ($item->user_id === $userId) {
                return $item;
            }
        }
    }

    /**
     * Get a new Rank with just a summary of the positions.
     */
    public function summarize(): RankInterface
    {
        $summarizedRank = new static(RankSlicer::process($this));

        $summarizedRank->isGlobal = $this->isGlobal;

        return $summarizedRank;
    }

    /**
     * Add a RankItem to the current Rank.
     */
    private function registerRankItem($item, int $index)
    {
        $freshItem = clone $item;
        $freshItem->position = $index + 1;

        $this->items[$index] = RankItem::create($freshItem);
    }
}
