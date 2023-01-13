<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataCollectorRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Service\InvalidIdentifierHandlerRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface as CoreRegistryInterface;

interface RegistryInterface extends
    CoreRegistryInterface,
    InvalidIdentifierHandlerRegistryInterface,
    DataCollectorRegistryInterface
{
    public function getCollector(): CollectorInterface;
}
