<?php
declare(strict_types = 1);

namespace UwKluis\WebhookClient\Receive;

use DateTime;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UwKluis\WebhookClient\Exception\InvalidSignatureException;

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
        callable|null $callable = null
    ): ResponseInterface {
        $body = (string) $request->getBody();
        $digestable = $body . $secret;
        $digest = hash_hmac('sha256', $digestable, $secret);
        if ($request->getHeader('X-Hook-Signature')
            && hash_equals($request->getHeader('X-Hook-Signature')[0], $digest)) {
            if (is_callable($callable)) {
                $callable($this->parseMessage((array) json_decode($body, true)));
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
        /** @var array $metadata */
        $metadata = $message['metadata'];
        $dateTime = DateTime::createFromFormat('U', (string) $metadata['timestamp']);
        if (!$dateTime instanceof DateTime) {
            throw new \UnexpectedValueException('The timestamp is not parseable to a DateTime object');
        }

        return (new Message())
            ->setData($message['data'])
            ->setWebhookId((int) $metadata['webhook_id'])
            ->setIdentifier((string) $metadata['identifier'])
            ->setTries((int) $metadata['tries'])
            ->setEvent((string) $metadata['event'])
            ->setSequence((int) $metadata['sequence'])
            ->setTimestamp($dateTime);
    }
}
