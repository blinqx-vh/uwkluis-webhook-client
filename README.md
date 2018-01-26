# webhook-client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabsInsight][ico-sensiolabs]][link-sensiolabs]

This is a client to easily handle incoming webhooks produced by the ufo/webhooks package.

## Structure

```
src/
src/Manage/
src/Exception/
src/Receive/
```

## Install

Via Composer

``` bash
$ composer require ufo/webhook-client
```


## Usage
Processing an incoming webhook:

``` php
$function = function (Message $message) {
    $this->logger->log($message->getData());
};

/** @var \Psr\Http\Message\ResponseInterface */
$response = new PsrResponse();

$processor = new \Ufo\WebhookClient\Receive\Processor();
return $processor->process(
    $request,
    $response, 
    $storedIncomingWebhook->getSecret(),
    $function    
);
```

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email it@mijnufo.nl instead of using the issue tracker.

## Credits

- [THE UFO TEAM][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ufo/webhook-client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ufo/webhook-client/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/ufo/webhook-client.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/ufo/webhook-client.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ufo/webhook-client.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/d7a6387a-192c-4614-9c74-a4889cdfa68d.svg 

[link-packagist]: https://packagist.org/packages/ufo/webhook-client
[link-scrutinizer]: https://scrutinizer-ci.com/g/ufo/webhook-client/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/ufo/webhook-client
[link-downloads]: https://packagist.org/packages/ufo/webhook-client
[link-author]: https://bitbucket.org/hypotheekbond/ufo-webhook-client
[link-contributors]: ../../contributors
[link-sensiolabs]: https://insight.sensiolabs.com/projects/d7a6387a-192c-4614-9c74-a4889cdfa68d
