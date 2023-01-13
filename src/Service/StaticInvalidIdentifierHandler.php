<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;

class StaticInvalidIdentifierHandler extends InvalidIdentifierHandler
{
    protected const PENALTY = 5;

    protected function getPenalty(int $invalidRequestCount): int
    {
        return static::PENALTY;
    }

    protected function getInvalidRequestCount(): int
    {
        return 0;
    }

    protected function setInvalidRequestCount(int $invalidRequestCount): void
    {
    }
}
