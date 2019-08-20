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
     *
     */
    public function __construct(array $items)
    {
        array_walk($items, [$this, 'registerRankItem']);
    }

    /**
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    public function itemsCount(): int
    {
        return count($this->items);
    }

    /**
     *
     */
    public function hasNext(RankItem $item): bool
    {
        return isset($this->items[$item->position]);
    }

    /**
     *
     */
    public function getUserPosition(?int $userId = null): ?int
    {
        $userId = $userId ?? auth()->user()->id;

        return array_search($this->getUserRankItem($userId), $this->items) + 1 ?: null;
    }

    /**
     *
     */
    public function getUserRankItem(?int $userId = null): RankItem
    {
        $userId = $userId ?? auth()->user()->id;

        foreach ($this->items as $item) {
            if ($item->user_id === $userId) {
                return $item;
            }
        }
    }

    /**
     *
     */
    public function getFormattedUserPosition(?int $userId = null): string
    {
        $userId = $userId ?? auth()->user()->id;

        $userRank = $this->getUserPosition($userId);

        $formatter = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        return $formatter->format($userRank);
    }

    /**
     *
     */
    public function getCountryRank(?int $countryId = null): RankInterface
    {
        $countryId = $countryId ?? auth()->user()->country_id;

        $countryItems = array_filter($this->items, function(RankItem $item) use ($countryId) {
            return $item->country_id === $countryId;
        });

        return new static(array_values($countryItems));
    }

    /**
     *
     */
    public function getPreviewRank(): RankInterface
    {
        return new static(RankSlicer::process($this));
    }

    private function registerRankItem($item, int $index)
    {
        $freshItem = clone $item;
        $freshItem->position = $index + 1;

        $this->items[$index] = RankItem::create($freshItem);
    }
}
