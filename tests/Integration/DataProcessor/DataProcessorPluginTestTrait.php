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
        $initialization->initMetaData($this->registry);
        $initialization->initGlobalConfiguration(RegistryDomain::CORE, $this->registry);
        $initialization->initServices(RegistryDomain::CORE, $this->registry);
        $initialization->initPlugins(RegistryDomain::CORE, $this->registry);
    }
}
