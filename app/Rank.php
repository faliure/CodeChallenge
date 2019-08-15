<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    /**
     * @var array
     */
    private $_rankItems = [];

    /**
     * @var array
     */
    private $rankItemValues = [];

    /**
     *
     */
    public function __construct(array $rankItems)
    {
        // Add position attribute to each rank item
        array_walk($rankItems, function($rankItem, $index) {
            $newRankItem = clone $rankItem;
            $newRankItem->position = $index + 1;

            $this->_rankItems[$index] = $newRankItem;
        });

        // For outside use we don't need the positions as index
        $this->rankItemValues = array_values($this->_rankItems);
    }

    /**
     * When someone requests the rankItems list, return the regular array
     * (i.e. the one with consecutive indexes starting from zero).
     *
     * @var $name  The name of the undefined property being accessed
     *
     * @return array|null
     */
    public function __get($name)
    {
        if ($name === 'rankItems') {
            return $this->rankItemValues;
        }
    }

    /**
     *
     */
    public function hasNextPosition(int $currentPosition)
    {
        return isset($this->_rankItems[$currentPosition]);
    }

    /**
     *
     */
    public function getUserRank(?int $userId = null) : ?int
    {
        $userId = $userId ?? auth()->user()->id;

        return array_search($this->getRankItem($userId), $this->_rankItems) + 1 ?: null;
    }

    /**
     *
     */
    public function getRankItem(?int $userId = null) : \stdClass
    {
        $userId = $userId ?? auth()->user()->id;

        return array_column($this->_rankItems, null, 'user_id')[$userId];
    }

    /**
     *
     */
    public function getFormattedUserRank(?int $userId = null) : string
    {
        $userId = $userId ?? auth()->user()->id;

        $userRank = $this->getUserRank($userId);

        $formatter = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

        return $formatter->format($userRank);
    }

    /**
     *
     */
    public function getCountryRank(?int $countryId = null) : Rank
    {
        $countryId = $countryId ?? auth()->user()->country_id;

        $countryRank = array_values(
            array_filter($this->_rankItems, function($rankItem) use ($countryId) {
                return $rankItem->country_id === $countryId;
            })
        );

        return new static($countryRank);
    }

    /**
     *
     */
    public function getPreviewRank() : Rank
    {
        if (count($this->_rankItems) <= 9) {
            return new static($this->_rankItems);
        }

        $rankItems = array_slice($this->_rankItems, 0, 3, true)                                 // First-3 slice
                   + array_slice($this->_rankItems, max(0, $this->getUserRank() - 2), 3, true)  // User-centred slice
                   + $this->getMiddleSlice()                                                    // Middle slice
                   + array_slice($this->_rankItems, -3, 3, true);                               // Last-3 slice

        return new static($rankItems);
    }

    private function getMiddleSlice() : array
    {
        $userRank = $this->getUserRank();
        $itemsCount = count($this->_rankItems);

        if ($userRank > 4 && $userRank < $itemsCount - 3) {
            return []; // No middle slice required
        }

        return array_slice(
            $this->_rankItems,                                          // Full rank
            floor(count($this->_rankItems) / 2) - 1,                    // Middle slice offset
            min(3, 4 - min($userRank - 1, $itemsCount - $userRank)),    // Middle slice length
            true                                                        // Preserve keys (position)
        );
    }
}
