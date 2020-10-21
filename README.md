# webhook-client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

This is a client to easily handle incoming webhooks produced by the uwkluis/webhooks package.

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
$ composer require uwkluis/webhook-client
```


## Usage
Processing an incoming webhook:

``` php
$function = function (Message $message) {
    $this->logger->log($message->getData());
};

/** @var \Psr\Http\Message\ResponseInterface */
$response = new PsrResponse();

$processor = new \UwKluis\WebhookClient\Receive\Processor();
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

Please see [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email it@hypotheekbond.nl instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/uwkluis/webhook-client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/uwkluis/webhook-client/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/uwkluis/webhook-client.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/uwkluis/webhook-client
[link-scrutinizer]: https://scrutinizer-ci.com/g/uwkluis/webhook-client/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/uwkluis/webhook-client
[link-downloads]: https://packagist.org/packages/uwkluis/webhook-client
[link-downloads]: https://packagist.org/packages/uwkluis/webhook-client
