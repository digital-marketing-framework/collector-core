<?php

namespace DigitalMarketingFramework\Collector\Core\DataCollector;

interface IdentifierInterface
{
    public function getDomainKey(): string;
    public function getCacheKey(): string;
    public function getPayload(): array;
}
