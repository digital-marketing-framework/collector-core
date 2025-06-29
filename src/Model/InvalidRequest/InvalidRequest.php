<?php

namespace DigitalMarketingFramework\Collector\Core\Model\InvalidRequest;

use DateTime;
use DigitalMarketingFramework\Core\Model\Item;

class InvalidRequest extends Item implements InvalidRequestInterface
{
    public function __construct(
        protected DateTime $tstamp = new DateTime(),
        protected string $identifier = '',
        protected int $count = 0,
    ) {
    }

    public function getLabel(): string
    {
        return $this->getIdentifier();
    }

    public function setTstamp(DateTime $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    public function getTstamp(): DateTime
    {
        return $this->tstamp;
    }

    public function isExpired(int $timeout = 60): bool
    {
        return $this->tstamp->getTimestamp() + $timeout < time();
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
