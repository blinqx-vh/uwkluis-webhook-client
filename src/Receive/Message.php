<?php
declare(strict_types = 1);

namespace UwKluis\WebhookClient\Receive;

use DateTime;

final class Message
{
    /** @var array */
    private $data;
    /** @var int */
    private $webhookId;
    /** @var string */
    private $identifier;
    /** @var int */
    private $tries;
    /** @var string */
    private $event;
    /** @var int */
    private $sequence;
    /** @var DateTime */
    private $timestamp;

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * @return int
     */
    public function getWebhookId(): int
    {
        return $this->webhookId;
    }

    /**
     * @param int $webhookId
     *
     * @return Message
     */
    public function setWebhookId(int $webhookId): Message
    {
        $this->webhookId = $webhookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return Message
     */
    public function setIdentifier(string $identifier): Message
    {
        $this->identifier = $identifier;

        return $this;
    }


    /**
     * @return int
     */
    public function getTries(): int
    {
        return $this->tries;
    }

    /**
     * @param int $tries
     *
     * @return Message
     */
    public function setTries(int $tries): Message
    {
        $this->tries = $tries;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     *
     * @return Message
     */
    public function setEvent(string $event): Message
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     *
     * @return Message
     */
    public function setSequence(int $sequence): Message
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     *
     * @return Message
     */
    public function setTimestamp(DateTime $timestamp): Message
    {
        $this->timestamp = $timestamp;

        return $this;
    }


}
