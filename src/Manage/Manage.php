<?php
declare(strict_types = 1);

namespace UwKluis\WebhookClient\Manage;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use \GuzzleHttp\ClientInterface as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\WebhookClient\Receive\Processor;

class Manage
{
    /** @var string */
    private $baseUri;
    /** @var GuzzleClient */
    private $guzzleClient;

    /**
     * Manage constructor.
     *
     * @param string       $baseUri
     * @param GuzzleClient $guzzleClient
     */
    public function __construct(
        string $baseUri,
        GuzzleClient $guzzleClient
    ) {
        $this->baseUri = $baseUri;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param Token $accessToken
     *
     * @throws BadResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array
     */
    public function list(Token $accessToken): array
    {
        return json_decode($this->guzzleClient->request(
            RequestMethodInterface::METHOD_GET,
            $this->baseUri . '/webhook',
            [
                RequestOptions::HEADERS => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken->toString(),
                ],
            ]
        )->getBody()->getContents(), true);
    }

    /**
     * @param Token $accessToken
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listAvailable(Token $accessToken): array
    {
        return json_decode(
            $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                $this->baseUri . '/webhook/available',
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents(),
            true
        );
    }

    /**
     * @param Token  $accessToken
     * @param string $targetUri
     * @param string $identifier
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Token $accessToken, string $targetUri, string $identifier): array
    {
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $this->guzzleClient->request(
            RequestMethodInterface::METHOD_POST,
            $this->baseUri . '/webhook',
            [
                RequestOptions::HEADERS     => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken->toString(),
                ],
                RequestOptions::FORM_PARAMS => [
                    'target_uri' => $targetUri,
                    'identifier' => $identifier,
                ],
            ]
        );

        $decodedResponse = json_decode($response->getBody()->getContents(), true);
        $decodedResponse['secret'] = $response->getHeader('X-Hook-Secret')[0];

        return $decodedResponse;
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @throws BadResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array
     */
    public function get(Token $accessToken, int $id): array
    {
        return json_decode(
            $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                $this->baseUri . '/webhook/' . $id,
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents(),
            true
        );
    }

    /**
     * @param Token  $accessToken
     * @param int    $id
     * @param string $targetUri
     * @param string $identifier
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(Token $accessToken, int $id, string $targetUri, string $identifier): array
    {
        return json_decode(
            $this->guzzleClient->request(
                RequestMethodInterface::METHOD_PUT,
                $this->baseUri . '/webhook/' . $id . '?' . http_build_query([
                    'target_uri' => $targetUri,
                    'identifier' => $identifier,
                ]),
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents(),
            true
        );
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @throws BadResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return bool
     */
    public function delete(Token $accessToken, int $id): bool
    {
        return $this->guzzleClient->request(
            RequestMethodInterface::METHOD_DELETE,
            $this->baseUri . '/webhook/' . $id,
            [
                RequestOptions::HEADERS => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken->toString(),
                ],
            ]
        )->getStatusCode() === StatusCodeInterface::STATUS_NO_CONTENT;
    }

    /**
     * @param Token     $accessToken
     * @param Processor $processor
     *
     * @throws BadResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array
     */
    public function claimCheck(Token $accessToken, Processor $processor): array
    {
        return array_map(
            [$processor, 'parseMessage'],
            json_decode($this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                $this->baseUri . '/webhook/claim-check',
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents(), true)['data']
        );
    }

    /**
     * @param string $baseUri
     *
     * @return Manage
     */
    public function setBaseUri(string $baseUri): Manage
    {
        $this->baseUri = $baseUri;

        return $this;
    }
}
