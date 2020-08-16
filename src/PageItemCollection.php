<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


use ArrayIterator;
use IteratorAggregate;

class PageItemCollection implements IteratorAggregate
{
    /**
     * @var PageItem[]
     */
    protected $pages = [];

    public function set(PageItem $val): void
    {
        $this->pages[] = $val;
    }

    public function get($key): PageItem
    {
        return $this->pages[$key];
    }

    public function hasPage(PageItem $page): bool
    {
        foreach ($this->pages as $item) {
            if ($item->getLink() === $page->getLink()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->pages);
    }
}