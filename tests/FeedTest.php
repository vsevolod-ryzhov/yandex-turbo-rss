<?php

declare(strict_types=1);


namespace vsevolodryzhov\YandexTurboRss;


use PHPUnit\Framework\TestCase;
use XMLWriter;

class FeedTest extends TestCase
{
    public function testSuccess(): void
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

    public function testDuplicateAnalytics(): void
    {
        $feed = new Feed(
            'Test feed',
            'https://eot.company',
            'Test description',
            'ru'
        );
        $feed->setAnalytics(new Analytics('Yandex', '12345'));
        $this->expectExceptionMessage('This analytics is already exists');
        $feed->setAnalytics(new Analytics('Yandex', '12345'));
    }

    public function testDuplicatePage(): void
    {
        $feed = new Feed(
            'Test feed',
            'https://eot.company',
            'Test description',
            'ru'
        );
        $feed->setPage(new PageItem('Duplicate page', 'https://eot.company/duplicate', '<p>Duplicate page content</p>'));
        $this->expectExceptionMessage('This page is already exists');
        $feed->setPage(new PageItem('Duplicate page', 'https://eot.company/duplicate', '<p>Duplicate page content</p>'));
    }
}