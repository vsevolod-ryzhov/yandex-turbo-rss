<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


use PHPUnit\Framework\TestCase;
use XMLWriter;

class FeedTest extends TestCase
{
    public function testSuccess()
    {
        $feed = new Feed(
            'Test feed',
            'https://eot.company',
            'Test description',
            'ru'
        );
        $feed->setAnalytics(new Analytics('Yandex', '12345'));

        $feed->setPages([
            new PageItem('First test Page', 'https://eot.company', '<p>Test content</p>')
        ]);

        $this::assertInstanceOf(XMLWriter::class, $feed->make());
    }
}