<?php
declare(strict_types = 1);

namespace Ufo\WebhookClient\Manage;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use Lcobucci\JWT\Token;
use Ufo\WebhookClient\Receive\Processor;

final class Manage
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
    public function __construct(string $baseUri, GuzzleClient $guzzleClient)
    {
        $this->baseUri = $baseUri;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param Token $accessToken
     *
     * @throws BadResponseException
     * @return array
     */
    public function list(Token $accessToken): array
    {
        $httpResponse =
            $this->guzzleClient->get($this->baseUri . '/webhook',
                [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ])->getBody()->getContents();

        return json_decode($httpResponse, true);
    }

    /**
     * @param Token  $accessToken
     * @param string $targetUri
     * @param string $identifier
     *
     * @return array
     */
    public function post(Token $accessToken, string $targetUri, string $identifier): array
    {
        $data = [
            'target_uri' => $targetUri,
            'identifier' => $identifier,
        ];
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $this->guzzleClient->post($this->baseUri . '/webhook',
            [
                'headers'     => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . (string) $accessToken,
                ],
                'form_params' => $data,
            ]);
        $httpResponseBody = $response->getBody()->getContents();

        $decodedResponse = json_decode($httpResponseBody, true);
        $decodedResponse['secret'] = $response->getHeader('X-Hook-Secret')[0];
        return $decodedResponse;
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @throws BadResponseException
     * @return array
     */
    public function get(Token $accessToken, int $id): array
    {
        $httpResponse =
            $this->guzzleClient->get($this->baseUri . '/webhook/' . $id,
                [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ])->getBody()->getContents();

        return json_decode($httpResponse, true);
    }

    /**
     * @param Token  $accessToken
     * @param int    $id
     * @param string $targetUri
     * @param string $identifier
     *
     * @return array
     */
    public function put(Token $accessToken, int $id, string $targetUri, string $identifier): array
    {
        $query = [
            'target_uri' => $targetUri,
            'identifier' => $identifier,
        ];
        $queryString = http_build_query($query);
        $httpResponse =
            $this->guzzleClient->put($this->baseUri . '/webhook/' . $id . '?' . $queryString,
                [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ])->getBody()->getContents();

        return json_decode($httpResponse, true);
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @throws BadResponseException
     * @return bool
     */
    public function delete(Token $accessToken, int $id): bool
    {
        $httpResponse =
            $this->guzzleClient->delete($this->baseUri . '/webhook/' . $id,
                [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ])->getBody()->getContents();

        $result = json_decode($httpResponse, true);
        return $result === 'success';
    }

    /**
     * @param Token     $accessToken
     * @param Processor $processor
     *
     * @throws BadResponseException
     * @return array
     */
    public function claimCheck(Token $accessToken, Processor $processor): array
    {
        $httpResponse =
            $this->guzzleClient->get($this->baseUri . '/webhook/claim-check',
                [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ])->getBody()->getContents();
        /** @var array $responseMessages */
        $responseMessages = json_decode($httpResponse, true)['data'];
        $messages = [];
        foreach ($responseMessages as $message) {
            $messages[] = $processor->parseMessage($message);
        }

        return $messages;
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
