<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


class Analytics
{
    private $type;
    private $id;

    public function __construct(string $type, string $id)
    {

        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}