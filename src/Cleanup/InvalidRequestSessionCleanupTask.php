<?php

namespace DigitalMarketingFramework\Collector\Core\Cleanup;

use DigitalMarketingFramework\Collector\Core\InvalidIdentifier\InvalidIdentifierHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface as CollectorRegistryInterface;
use DigitalMarketingFramework\Core\Cleanup\CleanupTask;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class InvalidRequestSessionCleanupTask extends CleanupTask
{
    protected InvalidIdentifierHandlerInterface $invalidIdentifierHandler;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        $collectorRegistry = $registry->getRegistryCollection()->getRegistryByClass(CollectorRegistryInterface::class);
        $this->invalidIdentifierHandler = $collectorRegistry->getInvalidIdentifierHandler();

        parent::__construct($keyword);
    }

    public function execute(): void
    {
        $this->invalidIdentifierHandler->cleanup();
    }
}
