<?php

namespace App;

final class RankItem
{
    const REQUIRED_PROPERTIES = [
        'user_id',
        'user_name',
        'country_id',
        'position',
    ];

    /**
     *
     */
    private $item;

    private function __construct() {}

    public static function create($item)
    {
        if ($item instanceof static) {
            return $item;
        }

        return (new static)->validate($item)->register($item);
    }

    private function validate($item): RankItem
    {
        if (!is_object($item)) {
            throw new \InvalidArgumentException('RankItem input must be an object');
        }

        foreach (static::REQUIRED_PROPERTIES as $property) {
            if (!isset($item->$property)) {
                throw new \InvalidArgumentException(
                    'Invalid rankItem: missing required property "' . $property . '"'
                );
            }
        }

        return $this;
    }

    private function register($item): RankItem
    {
        $this->item = $item;

        return $this;
    }

    public function __get(string $name)
    {
        return $this->item->$name ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->item->$name = $value;
    }
}
