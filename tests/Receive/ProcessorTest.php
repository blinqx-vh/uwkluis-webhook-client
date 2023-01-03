<?php
declare(strict_types = 1);


namespace UwKluis\WebhookClient\Receive;

use GuzzleHttp\Psr7\BufferStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use UwKluis\WebhookClient\Exception\InvalidSignatureException;

class ProcessorTest extends TestCase
{
    /** @var array */
    private $message;
    /** @var Message */
    private $parsedMessage;

    protected function setUp(): void
    {
        $webhookId = 1;
        $identifier = 'foo.created';
        $tries = 1;
        $event = '000111222333';
        $sequence = 1;
        $this->message = [
            'data'     => [],
            'metadata' => [
                'webhook_id' => $webhookId,
                'identifier' => $identifier,
                'tries'      => $tries,
                'event'      => $event,
                'sequence'   => $sequence,
                'timestamp'  => 1,
            ],
        ];
    }


    public function testParseMessage()
    {
        $processor = new Processor();

        $webhookId = 1;
        $identifier = 'foo.created';
        $tries = 1;
        $event = '000111222333';
        $sequence = 1;
        $parsedMessage = $processor->parseMessage(
            $this->message
        );
        $this->assertInstanceOf(Message::class, $parsedMessage);
        $this->assertEmpty($parsedMessage->getData());
        $this->assertEquals($parsedMessage->getWebhookId(), $webhookId);
        $this->assertEquals($parsedMessage->getIdentifier(), $identifier);
        $this->assertEquals($parsedMessage->getTries(), $tries);
        $this->assertEquals($parsedMessage->getEvent(), $event);
        $this->assertEquals($parsedMessage->getSequence(), $sequence);
        $this->assertInstanceOf(\DateTime::class, $parsedMessage->getTimestamp());
        $this->assertEquals(new \DateTime('1970-01-01 00:00:01'), $parsedMessage->getTimestamp());

        $this->message['metadata']['timestamp'] = 'a';
        $this->expectException('UnexpectedValueException');
        $processor->parseMessage($this->message);
    }

    public function testProcessCorrectSecret()
    {
        $processor = new Processor();
        $secret = 'secret';
        $jsonBody = json_encode($this->message);
        $digestable = $jsonBody . $secret;
        $digest = hash_hmac('sha256', $digestable, $secret);
        $bufferStream = new BufferStream();
        $bufferStream->write($jsonBody);
        $testRequest = (new ServerRequest('post', 'test'))
            ->withHeader('X-Hook-Signature', $digest)
            ->withBody($bufferStream);
        $testResponse = new Response();
        $function = function (Message $message) {
            $this->parsedMessage = $message;
        };
        try {
            /** @noinspection PhpParamsInspection */
            $response = $processor->process($testRequest, $testResponse, $secret, $function);
        } catch (InvalidSignatureException $e) {
            //Ignore
        }
        $this->assertTrue(isset($response));
        $this->assertInstanceOf(Message::class, $this->parsedMessage);
    }

    public function testProcessWrongSecret()
    {
        $processor = new Processor();
        $secret = 'secret';
        $jsonBody = json_encode($this->message);
        $digestable = $jsonBody . $secret;
        $digest = hash_hmac('sha256', $digestable, $secret);
        $bufferStream = new BufferStream();
        $bufferStream->write($jsonBody);
        $testRequest = (new ServerRequest('post', 'test'))
            ->withHeader('X-Hook-Signature', $digest)
            ->withBody($bufferStream);
        $testResponse = new Response();
        try {
            /** @noinspection PhpParamsInspection */
            $processor->process($testRequest, $testResponse, 'wrongSecret');
        } catch (InvalidSignatureException $e) {
            $this->assertTrue(isset($e));
            $this->assertInstanceOf(InvalidSignatureException::class, $e);
            $this->assertEquals($testRequest, $e->getRequest());
        }
    }
}
