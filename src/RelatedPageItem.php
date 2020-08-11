<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


class RelatedPageItem
{
    private $url;
    private $img;
    private $text;

    public function __construct(string $url, string $img, string $text) {

        $this->url = $url;
        $this->img = $img;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getImg(): string
    {
        return $this->img;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}