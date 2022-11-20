<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\Registry\Plugin\CollectorRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Service\CollectorServiceInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;

interface RegistryInterface extends CollectorRegistryInterface, CacheRegistryInterface
{
    public function getCollectorService(): CollectorServiceInterface;
}
