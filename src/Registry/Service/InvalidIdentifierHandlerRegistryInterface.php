<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Service\InvalidIdentifierHandlerInterface;

interface InvalidIdentifierHandlerRegistryInterface
{
    public function getInvalidIdentifierHandler(): InvalidIdentifierHandlerInterface;
    public function setInvalidIdentifierHandler(InvalidIdentifierHandlerInterface $invalidIdentifierHandler): void;
}
