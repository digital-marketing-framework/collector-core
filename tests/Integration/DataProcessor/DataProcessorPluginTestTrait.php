<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration\DataProcessor;

use DigitalMarketingFramework\Collector\Core\CollectorCoreInitialization;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;

trait DataProcessorPluginTestTrait
{
    protected function initRegistry(): void
    {
        parent::initRegistry();
        $initialization = new CollectorCoreInitialization();
        $initialization->init(RegistryDomain::CORE, $this->registry);
    }
}
