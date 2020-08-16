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
        $feed->setAnalytics(new Analytics('Google', '54321'));

        $feed->setPage(new PageItem('First test Page', 'https://eot.company/test', '<p>Test content</p>'));

        $this::assertInstanceOf(XMLWriter::class, $feed->make());
        $output = $feed->make()->outputMemory();
        $this::assertStringContainsString('First test Page', $output);
        $this::assertStringContainsString('https://eot.company/test', $output);
        $this::assertStringContainsString('<p>Test content</p>', $output);

        $this::assertStringContainsString('Yandex', $output);
        $this::assertStringContainsString('Google', $output);
    }
}