<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration\DataProcessor;

use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Collector\Core\CollectorCoreInitialization;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait DataProcessorPluginTestTrait
{
    protected function initRegistry(): void
    {
        parent::initRegistry();
        $initialization = new CollectorCoreInitialization();
        $initialization->init(RegistryDomain::CORE, $this->registry);
    }
}
