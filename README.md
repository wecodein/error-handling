# error-handling

[![Build Status][ico-build]][link-build]
[![Code Quality][ico-code-quality]][link-code-quality]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Latest Version][ico-version]][link-packagist]
[![PDS Skeleton][ico-pds]][link-pds]

## Installation

The preferred method of installation is via [Composer](http://getcomposer.org/). Run the following command to install the latest version of a package and add it to your project's `composer.json`:

```bash
composer require wecodein/error-handling
```

## Usage

``` php
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Handler\BlockingErrorHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;

require __DIR__ . '/../vendor/autoload.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$processor = new CallableProcessor(function (Throwable $throwable) : Throwable {
    // log, emmit...
    return $throwable;
});

$errorHandler = new BlockingErrorHandler();
$errorHandler->register();

$exceptionHandler = new ExceptionHandler($processor);
$exceptionHandler->register();

$fatalErrorHandler = new FatalErrorHandler(20, $processor);
$fatalErrorHandler->register();

trigger_error('Error');

```

## Credits

- [Dusan Vejin][link-author]
- [All Contributors][link-contributors]

## License

Released under MIT License - see the [License File](LICENSE) for details.


[ico-version]: https://img.shields.io/packagist/v/wecodein/error-handling.svg
[ico-build]: https://travis-ci.org/wecodein/error-handling.svg?branch=master
[ico-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/wecodein/error-handling.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/wecodein/error-handling.svg
[ico-pds]: https://img.shields.io/badge/pds-skeleton-blue.svg

[link-packagist]: https://packagist.org/packages/wecodein/error-handling
[link-build]: https://travis-ci.org/wecodein/error-handling
[link-code-coverage]: https://scrutinizer-ci.com/g/wecodein/error-handling/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/wecodein/error-handling
[link-pds]: https://github.com/php-pds/skeleton
[link-author]: https://github.com/dutekvejin
[link-contributors]: ../../contributors
