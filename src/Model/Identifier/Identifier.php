<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Indentifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Model\Indentifier\CacheIdentifier;

abstract class Identifier extends CacheIdentifier implements IdentifierInterface
{
    public function __construct(
        protected array $payload = [],
    ) {
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function pack(): array
    {
        return $this->payload;
    }

    public static function unpack(array $packed): ValueInterface
    {
        return new static($packed);
    }
}
