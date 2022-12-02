<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataCollectorRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;

interface RegistryInterface extends 
    ConfigurationResolverRegistryInterface, 
    DataCollectorRegistryInterface
{
    public function getCollector(): CollectorInterface;
    public function getDefaultConfiguration(): array;
}
