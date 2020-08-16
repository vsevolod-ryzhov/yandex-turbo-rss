# Yandex Turbo Pages rss feed

Sync data with Yandex Turbo Pages rss feed

## Installation

Via Composer
```
composer require vsevolod-ryzhov/yandex-turbo-rss
```

## Usage

See more information in an official documentation: https://yandex.ru/dev/turbo/doc/rss/markup-docpage/#example

1. Create new rss feed
```php
$feed = new Feed(
    'Feed name',
    'https://<feed url address>',
    'Feed description',
    'Feed language'
);
```

2. Add analytics
```php
$feed->setAnalytics(new Analytics('Yandex', '12345'));
```

3. Create pages objects and add them to feed object
```php
$page1 = new PageItem('First test Page', 'https://eot.company/en', '<p>Test content</p>');
$feed->setPage($page1);

$page2 = new PageItem('Senond test Page', 'https://eot.company/ru', '<header><h1>Title</h1></header><p>Content</p><div><img class="lazyload" src="" data-src="https://eot.company/themes/classic/images/logo-en-black.svg" alt="test" /></div>');
$feed->setPage($page2);

$page2->setContentModifiers(static function ($content) { return str_replace(array('src=""', 'data-src="'), array('', 'src="'), $content); });

```

4. Make feed and output (or write to file)
```php
$xml = $feed->make(); // this will return XMLWriter object

// to make web Response:
$feed->make()->outputMemory()
```