<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Indentifier;

use DigitalMarketingFramework\Core\Model\Indentifier\CacheIdentifierInterface;

interface IdentifierInterface extends CacheIdentifierInterface
{
    public function getPayload(): array;
}
