<?php

declare(strict_types=1);

namespace vsevolodryzhov\YandexTurboRss;

use DateTimeImmutable;
use KubaWerlos\HtmlValidator\Validator;
use PHPUnit\Framework\TestCase;

class PageItemTest extends TestCase
{
    const TITLE = 'Test page title';
    const LINK = 'https://eot.company';

    /**
     * Wrap feed content for checking in html validator
     * @param $content
     * @return string
     */
    private function wrapHtml($content): string
    {
        $output = '<!DOCTYPE html>';
        $output .= '<html lang="en"><head><title>Test</title></head>';
        $output .= '<body>';
        $output .= $content;
        $output .= '</body>';
        return $output . '</html>';
    }

    /**
     * Test valid html content
     */
    public function testSimpleSuccess()
    {
        $content = '<header><h1>Title</h1></header><p>Content</p><div><img src="https://eot.company/themes/classic/images/logo-en-black.svg" alt="test" /></div>';
        $item = new PageItem(
            self::TITLE,
            self::LINK,
            $content
        );

        $this::assertEmpty(Validator::validate($this->wrapHtml($item->getContent())));
    }

    /**
     * Test valid html content with classes and inline styles
     */
    public function testComplexLayoutSuccess()
    {
        $content = '<header id="my-header-block" class="header header-alt" style="display: block; margin: 0"><h1 id="my-title" style="border-bottom: 1px solid #fff; padding: 0;" class="h1 text-primary">Title <span>with subtitle</span></h1></header><p>Content</p><div><img src="https://eot.company/themes/classic/images/logo-en-black.svg" alt="test" /></div>';
        $item = new PageItem(
            self::TITLE,
            self::LINK,
            $content
        );

        $this::assertEmpty(Validator::validate($this->wrapHtml($item->getContent())));
    }

    /**
     * Check custom html modifier function
     */
    public function testContentCallbackSuccess()
    {
        $content = '<header><h1>Title</h1></header><p>Content</p><div><img class="lazyload" src="" data-src="https://eot.company/themes/classic/images/logo-en-black.svg" alt="test" /></div>';
        $item = new PageItem(
            self::TITLE,
            self::LINK,
            $content
        );
        $item->setContentModifiers(static function ($content) { return str_replace(array('src=""', 'data-src="'), array('', 'src="'), $content); });

        $this::assertEmpty(Validator::validate($this->wrapHtml($item->getContent())));
    }

    /**
     * Check if `header > h1 > title` present in content
     */
    public function testEmptyHeaderSuccess()
    {
        $content = '<p>Content</p><div><img class="lazyload" src="" data-src="https://eot.company/themes/classic/images/logo-en-black.svg" alt="test" /></div>';
        $item = new PageItem(
            self::TITLE,
            self::LINK,
            $content
        );
        $item->setContentModifiers(static function ($content) { return str_replace(array('src=""', 'data-src="'), array('', 'src="'), $content); });

        $this::assertEmpty(Validator::validate($this->wrapHtml($item->getContent())));
        $this::assertStringStartsWith('<header><h1>Test page title</h1></header>', $item->getContent());
    }

    public function testPubDateSuccess()
    {
        $content = '<header><h1>Title</h1></header><p>Content</p><div><img src="https://eot.company/themes/classic/images/logo-en-black.svg" alt="test" /></div>';
        $item = new PageItem(
            self::TITLE,
            self::LINK,
            $content
        );
        $item->setPubDate(new DateTimeImmutable("30.08.2020 10:30:40"));
        $this::assertSame('Sun, 30 Aug 20 10:30:40 +0000', $item->getPubDate());
    }
}