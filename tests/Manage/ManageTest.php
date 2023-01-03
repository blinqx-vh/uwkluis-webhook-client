<?php

namespace UwKluis\WebhookClient\Manage;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use UwKluis\WebhookClient\Receive\Processor;

class ManageTest extends TestCase
{
    private $token;

    public function setUp(): void
    {
        $testDataSet = new Token\DataSet([], '');
        $this->token = new Token\Plain($testDataSet, $testDataSet, new Token\Signature('', ''));
    }

    /**
     *
     */
    public function testDelete()
    {

        $this->assertTrue(
            $this->mockApi(
                'delete',
                [$this->token, 1],
                [],
                StatusCodeInterface::STATUS_NO_CONTENT
            )
        );
    }

    /**
     *
     */
    public function testPost()
    {
        $this->assertEquals(
            [
                'secret' => 'foo',
            ],
            $this->mockApi(
                'post',
                [$this->token, 'foo.bar', 'baz'],
                [],
                StatusCodeInterface::STATUS_OK,
                ['X-Hook-Secret' => 'foo']
            )
        );
    }

    /**
     *
     */
    public function testPut()
    {
        $this->assertEquals([], $this->mockApi('put', [$this->token, 1, 'foo.bar', 'baz']));
    }

    /**
     *
     */
    public function testGet()
    {
        $this->assertEquals([], $this->mockApi('get', [$this->token, 1]));
    }

    /**
     *
     */
    public function testClaimCheck()
    {
        $this->assertEquals([], $this->mockApi('claimCheck', [$this->token, new Processor()], [
            'data' => [],
        ]));
    }

    /**
     *
     */
    public function testListAvailable()
    {
        $this->assertEquals([], $this->mockApi('listAvailable', [$this->token]));
    }

    /**
     *
     */
    public function testList()
    {
        $this->assertEquals([], $this->mockApi('list', [$this->token]));
    }

    /**
     * @param string $method
     * @param array  $params
     * @param array  $expectedResponse
     * @param int    $expectedCode
     * @param array  $expectedHeaders
     *
     * @return mixed
     */
    private function mockApi(
        string $method,
        array $params,
        array $expectedResponse = [],
        int $expectedCode = StatusCodeInterface::STATUS_OK,
        array $expectedHeaders = []
    ) {
        $mock = $this->getMockBuilder(Client::class)->getMock();
        $mock->method('request')
            ->willReturn(
                new Response(
                    $expectedCode,
                    $expectedHeaders,
                    json_encode($expectedResponse)
                )
            );
        /** @noinspection PhpParamsInspection */
        $manage = (new Manage(
            'foo',
            $mock
        ))->setBaseUri('bar');

        return call_user_func([$manage, $method], ...$params);
    }
}
