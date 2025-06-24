<?php

namespace DigitalMarketingFramework\Collector\Core\InvalidIdentifier;

use DigitalMarketingFramework\Core\Context\ContextInterface;

interface InvalidIdentifierHandlerInterface
{
    public function handleInvalidIdentifier(ContextInterface $context): void;

    public function handleValidIdentifier(ContextInterface $context): void;
}
