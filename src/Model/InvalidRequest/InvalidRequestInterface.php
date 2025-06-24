<?php

namespace DigitalMarketingFramework\Collector\Core\Model\InvalidRequest;

use DateTime;
use DigitalMarketingFramework\Core\Model\ItemInterface;

interface InvalidRequestInterface extends ItemInterface
{
    public function setTstamp(DateTime $tstamp): void;

    public function getTstamp(): DateTime;

    public function isExpired(int $timeout = 60): bool;

    public function setIdentifier(string $identifier): void;

    public function getIdentifier(): string;

    public function setCount(int $count): void;

    public function getCount(): int;
}
