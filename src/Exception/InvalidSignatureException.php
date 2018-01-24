<?php
declare(strict_types = 1);

namespace Ufo\WebhookClient\Exception;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

final class InvalidSignatureException extends RuntimeException
{
    /** @var ServerRequestInterface */
    private $request;

    /**
     * InvalidSignatureException constructor.
     *
     * @param string                 $message
     * @param int                    $code
     * @param Throwable|null         $previous
     * @param ServerRequestInterface $request
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null,
        ServerRequestInterface $request
    ) {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
