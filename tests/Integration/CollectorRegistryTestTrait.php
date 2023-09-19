<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration;

use DigitalMarketingFramework\Collector\Core\CollectorCoreInitialization;
use DigitalMarketingFramework\Collector\Core\Registry\Registry;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Core\Tests\Integration\RegistryTestTrait;

trait CollectorRegistryTestTrait // extends \PHPUnit\Framework\TestCase
{
    use RegistryTestTrait {
        initRegistry as initCoreRegistry;
    }

    protected Registry $registry;

    protected function createRegistry(): void
    {
        $this->registry = new Registry();
    }

    protected function initRegistry(): void
    {
        $this->initCoreRegistry();

        // init plugins
        $collectorCoreInitialization = new CollectorCoreInitialization();
        $collectorCoreInitialization->init(RegistryDomain::CORE, $this->registry);
        $collectorCoreInitialization->init(RegistryDomain::COLLECTOR, $this->registry);
    }
}
