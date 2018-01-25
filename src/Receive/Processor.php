<?php
declare(strict_types = 1);

namespace Ufo\WebhookClient\Receive;

use DateTime;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ufo\WebhookClient\Exception\InvalidSignatureException;

final class Processor
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param string                 $secret
     * @param callable|null          $callable
     *
     * @throws InvalidSignatureException
     *
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $secret,
        callable $callable = null
    ): ResponseInterface {
        $body = (string) $request->getBody();
        $digestable = $body . $secret;
        $digest = hash_hmac('sha256', $digestable, $secret);
        if ($request->getHeader('X-Hook-Signature')
            && hash_equals($request->getHeader('X-Hook-Signature')[0], $digest)) {
            if (is_callable($callable)) {
                $callable($this->parseMessage($request->getParsedBody()));
            }

            return $response
                ->withHeader('X-Hook-Secret', $secret)
                ->withStatus(StatusCodeInterface::STATUS_OK, 'received');
        }
        throw new InvalidSignatureException(
            'Verifying the request signature failed.',
            0,
            null,
            $request
        );
    }

    /**
     * @param array $message
     *
     * @return Message
     */
    public function parseMessage(array $message): Message
    {
        return (new Message())
            ->setData($message['data'])
            ->setWebhookId($message['metadata']['webhook_id'])
            ->setIdentifier($message['metadata']['identifier'])
            ->setTries($message['metadata']['tries'])
            ->setEvent($message['metadata']['event'])
            ->setSequence($message['metadata']['sequence'])
            ->setTimestamp(DateTime::createFromFormat('U', (string) $message['metadata']['timestamp']));
    }
}
