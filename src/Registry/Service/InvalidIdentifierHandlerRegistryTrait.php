<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\InvalidIdentifier\InvalidIdentifierHandlerInterface;
use DigitalMarketingFramework\Collector\Core\InvalidIdentifier\StaticInvalidIdentifierHandler;

trait InvalidIdentifierHandlerRegistryTrait
{
    protected InvalidIdentifierHandlerInterface $invalidIdentifierHandler;

    public function getInvalidIdentifierHandler(): InvalidIdentifierHandlerInterface
    {
        if (!isset($this->invalidIdentifierHandler)) {
            $this->invalidIdentifierHandler = new StaticInvalidIdentifierHandler();
        }

        return $this->invalidIdentifierHandler;
    }

    public function setInvalidIdentifierHandler(InvalidIdentifierHandlerInterface $invalidIdentifierHandler): void
    {
        $this->invalidIdentifierHandler = $invalidIdentifierHandler;
    }
}
