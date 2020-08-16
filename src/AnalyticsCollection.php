<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


use ArrayIterator;
use IteratorAggregate;

class AnalyticsCollection implements IteratorAggregate
{
    /**
     * @var Analytics[]
     */
    protected $items = [];

    public function set(Analytics $item): void
    {
        $this->items[] = $item;
    }

    public function get($key): Analytics
    {
        return $this->items[$key];
    }

    public function hasItem(Analytics $analytics): bool
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $analytics->getId() && $item->getType() === $analytics->getType()) {
                return true;
            }
        }

        return false;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}