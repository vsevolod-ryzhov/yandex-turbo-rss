<?php

declare(strict_types=1);

namespace vsevolodryzhov\YandexTurboRss;

class PageItem
{
    /**
     * @var string
     */
    private $defaultTitle;

    /**
     * @var string
     */
    private $link;

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
     * Check if `header` and `h1` tags exists and `h1` is present inside `header`
     * @param string $content
     * @return bool
     */
    private function contentHasTitle(string $content): bool
    {
        if (strpos($content, '<h1') === false || strpos($content, '<header') === false) {
            return false;
        }

        $pattern = '#<\s*?header\b[^>]*>(.*?)</header\b[^>]*>#s';
        preg_match($pattern, $content, $matches);
        return !(!$matches || strpos($matches[1], '<h1') === false);
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
        $this->defaultTitle = $defaultTitle;
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
    public function getContent(): string
    {
        $returnContent = $this->content;
        foreach ($this->contentModifiers as $contentModifier) {
            $returnContent = $contentModifier($returnContent);
        }

        if (!$this->contentHasTitle($returnContent)) {
            $returnContent = $this->appendTitle($returnContent, $this->defaultTitle);
        }
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
}