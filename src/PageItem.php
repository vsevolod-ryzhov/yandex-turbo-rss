<?php

declare(strict_types=1);

namespace vsevolodryzhov\YandexTurboRss;

use DateTimeImmutable;

class PageItem
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $link;

    /**
     * @var DateTimeImmutable
     */
    private $pubDate;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    private $contentModifiers = [];

    /**
     * @var array
     */
    private $related = [];

    /**
     * @var string
     */
    private $contentPrefix = '';

    /**
     * @var string
     */
    private $contentSuffix = '';

    /**
     * @param string $content
     * @return string|null
     */
    private function getTitleFromContent(string $content): ?string
    {
        if (strpos($content, '<h1') === false || strpos($content, '<header') === false) {
            return null;
        }

        // `h1` must be inside `header` tag. It's required rule by yandex
        $pattern = '#<\s*?header\b[^>]*>.*?<\s*?h1\b[^>]*>(.*?)</h1\b[^>]*>.*?</header\b[^>]*>#s';
        preg_match($pattern, $content, $matches);
        return ($matches && $matches[1]) ? strip_tags($matches[1]) : null;
    }

    /**
     * @param string $content
     * @param string $title
     * @return string
     */
    private function appendTitle(string $content, string $title): string
    {
        return "<header><h1>$title</h1></header>$content";
    }

    /**
     * PageItem constructor
     * @param string $defaultTitle Default title for page. It will be set up if there is no <h1>title</h1> is provided in content
     * @param string $link
     * @param string $content
     */
    public function __construct(string $defaultTitle, string $link, string $content)
    {
        // find title in content or set from constructor param if not founded
        $this->title = $this->getTitleFromContent($content) ?: $defaultTitle;

        $this->content = $content;
        $this->link = $link;
        $this->content = $content;
    }

    /**
     * @param RelatedPageItem $relatedPageItem
     */
    public function setRelated(RelatedPageItem $relatedPageItem): void
    {
        $this->related[] = $relatedPageItem;
    }

    /**
     * @return array
     */
    public function getRelated(): array
    {
        return $this->related;
    }

    /**
     * @return mixed
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $returnContent = $this->content;
        foreach ($this->contentModifiers as $contentModifier) {
            $returnContent = $contentModifier($returnContent);
        }

        $returnContent = $this->appendTitle($returnContent, $this->title);
        return $this->contentPrefix . $returnContent . $this->contentSuffix;
    }

    /**
     * @param string $contentSuffix
     */
    public function setContentSuffix(string $contentSuffix): void
    {
        $this->contentSuffix = $contentSuffix;
    }

    /**
     * @param string $contentPrefix
     */
    public function setContentPrefix(string $contentPrefix): void
    {
        $this->contentPrefix = $contentPrefix;
    }

    /**
     * @param callable $contentModifier
     */
    public function setContentModifiers(callable $contentModifier): void
    {
        $this->contentModifiers[] = $contentModifier;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPubDate(): ?string
    {
        return ($this->pubDate) ? $this->pubDate->format(DateTimeImmutable::RFC822) : null;
    }

    /**
     * @param DateTimeImmutable $pubDate
     */
    public function setPubDate(DateTimeImmutable $pubDate): void
    {
        $this->pubDate = $pubDate;
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }
}