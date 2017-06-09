# Buffered Console

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graze/buffered-console.svg?style=flat-square)](https://packagist.org/packages/graze/buffered-console)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/graze/buffered-console/master.svg?style=flat-square)](https://travis-ci.org/graze/buffered-console)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/graze/buffered-console.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/buffered-console/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/graze/buffered-console.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/buffered-console)
[![Total Downloads](https://img.shields.io/packagist/dt/graze/buffered-console.svg?style=flat-square)](https://packagist.org/packages/graze/buffered-console)

Update a multi-line console output, but only write what has changed

[![asciicast](https://asciinema.org/a/bokpbfbg8d4yolihmfimtoaqb.png)](https://asciinema.org/a/bokpbfbg8d4yolihmfimtoaqb)

## Usage

```php
$output = new BufferedConsoleOutput($existing);

$output->reWrite([
    'first line',
    'second line',
]);

$output->reWrite([
    'first line here',
    'second line',
]);
```

This will navigate the cursor to the end of `first line` and write ` here` then navigate the cursor back to the end.

 - Supports Symfony tags (e.g. `<info>`)

## Install

Via Composer

``` bash
$ composer require graze/buffered-console
```

## Testing

``` bash
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
