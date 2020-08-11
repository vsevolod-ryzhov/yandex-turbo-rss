<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


use XMLWriter;

class Feed
{
    /**
     * @var PageItem[]
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
     * @var Analytics|null
     */
    private $analytics;

    public function __construct(string $title, string $link, string $description, string $language)
    {

        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->language = $language;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @param Analytics $analytics
     */
    public function setAnalytics(Analytics $analytics): void
    {
        $this->analytics = $analytics;
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
        if (!$this->analytics) {
            return $xml;
        }

        $xml->startElement('turbo:analytics');
        $xml->writeAttribute('type', $this->analytics->getType());
        $xml->writeAttribute('id', $this->analytics->getId());
        $xml->endElement(); // turbo:analytics

        return $xml;
    }

    private function writePages(XMLWriter $xml): XMLWriter
    {
        foreach ($this->pages as $page) {
            $xml->startElement('item');
            $xml->writeAttribute('turbo', 'true');

            $xml->writeElement('link', $page->getLink());
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