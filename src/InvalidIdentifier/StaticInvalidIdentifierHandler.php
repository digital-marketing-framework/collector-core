<?php

namespace DigitalMarketingFramework\Collector\Core\InvalidIdentifier;

class StaticInvalidIdentifierHandler extends InvalidIdentifierHandler
{
    protected const PENALTY = 5;

    protected function enabled(): bool
    {
        return true;
    }

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
