<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


use DomainException;
use XMLWriter;

class Feed
{
    /**
     * @var PageItemCollection
     */
    private $pages;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $language;

    /**
     * @var AnalyticsCollection
     */
    private $analytics;

    public function __construct(string $title, string $link, string $description, string $language)
    {
        $this->pages = new PageItemCollection();
        $this->analytics = new AnalyticsCollection();
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->language = $language;
    }

    /**
     * @param PageItem $page
     */
    public function setPage(PageItem $page): void
    {
        if ($this->pages->hasPage($page)) {
            throw new DomainException('This page is already exists');
        }
        $this->pages->set($page);
    }

    /**
     * @param Analytics $analytics
     */
    public function setAnalytics(Analytics $analytics): void
    {
        if ($this->analytics->hasItem($analytics)) {
            throw new DomainException('This analytics is already exists');
        }
        $this->analytics->set($analytics);
    }

    private function writeFeedInformation(XMLWriter $xml): XMLWriter
    {
        $xml->writeElement('title', $this->title);
        $xml->writeElement('link', $this->link);
        $xml->writeElement('description', $this->description);
        $xml->writeElement('language', $this->language);

        return $xml;
    }

    /**
     * @param XMLWriter $xml
     * @return XMLWriter
     */
    private function writeAnalytics(XMLWriter $xml): XMLWriter
    {
        foreach ($this->analytics as $analytic) {
            $xml->startElement('turbo:analytics');
            $xml->writeAttribute('type', $analytic->getType());
            $xml->writeAttribute('id', $analytic->getId());
            $xml->endElement(); // turbo:analytics
        }

        return $xml;
    }

    private function writePages(XMLWriter $xml): XMLWriter
    {
        foreach ($this->pages as $page) {
            /* @var $page PageItem */
            $xml->startElement('item');
            $xml->writeAttribute('turbo', 'true');

            $xml->writeElement('link', $page->getLink());
            $xml->writeElement('turbo:topic', $page->getTitle());
            if (null !== ($pubDate = $page->getPubDate())) {
                $xml->writeElement('pubDate', $pubDate);
            }
            if (null !== ($author = $page->getAuthor())) {
                $xml->writeElement('author', $author);
            }
            $xml->startElement('turbo:content');
            $xml->writeCdata($page->getContent());
            $xml->endElement(); // turbo:content

            if ($relatedItems = $page->getRelated()) {
                $xml->startElement('yandex:related');
                foreach ($relatedItems as $relatedItem) {
                    /* @var $relatedItem RelatedPageItem */
                    $xml->startElement('link');
                    $xml->writeAttribute('url', $relatedItem->getUrl());
                    $xml->writeAttribute('img', $relatedItem->getImg());
                    $xml->text($relatedItem->getText());
                }
                $xml->endElement(); // yandex:related
            }
            $xml->endElement(); // item
        }

        return $xml;
    }

    public function make(): XMLWriter
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument("1.0", "UTF-8");

        $xml->startElement('rss');
        $xml->writeAttribute('xmlns:yandex', 'http://news.yandex.ru');
        $xml->writeAttribute('xmlns:media', 'http://search.yahoo.com/mrss/');
        $xml->writeAttribute('xmlns:turbo', 'http://turbo.yandex.ru');
        $xml->writeAttribute('version', '2.0');

        $xml->startElement('channel');

        // write common feed information
        $xml = $this->writeFeedInformation($xml);

        // write analytics information
        $xml = $this->writeAnalytics($xml);

        // write pages information
        $xml = $this->writePages($xml);

        $xml->endElement(); // channel

        $xml->endElement(); // rss

        $xml->endDocument();
        return $xml;
    }
}