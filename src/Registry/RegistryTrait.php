<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\Registry\Plugin\CollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Service\CollectorService;
use DigitalMarketingFramework\Collector\Core\Service\CollectorServiceInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryTrait;

trait RegistryTrait
{
    use CacheRegistryTrait;
    use CollectorRegistryTrait;

    protected CollectorServiceInterface $collectorService;

    public function getCollectorService(): CollectorServiceInterface
    {
        if (!isset($this->collectorService)) {
             $this->collectorService = $this->createObject(CollectorService::class, [$this, $this->cache]);
        }
        return $this->collectorService;
    }
}
