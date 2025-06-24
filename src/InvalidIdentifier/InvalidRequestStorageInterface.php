<?php

namespace DigitalMarketingFramework\Collector\Core\InvalidIdentifier;

use DigitalMarketingFramework\Collector\Core\Model\InvalidRequest\InvalidRequestInterface;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;

/**
 * @extends ItemStorageInterface<InvalidRequestInterface>
 */
interface InvalidRequestStorageInterface extends ItemStorageInterface
{
    /**
     * @return array<InvalidRequestInterface>
     */
    public function fetchExpired(int $expireTimestamp): array;

    public function fetchByIdentifier(string $identifier): ?InvalidRequestInterface;
}
