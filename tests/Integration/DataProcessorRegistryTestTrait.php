<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Collector\Core\CorePluginInitialization;

trait DataProcessorRegistryTestTrait // extends \DigitalMarketingFramework\Core\Tests\Integration\RegistryTestTrait
{
    protected RegistryInterface $registry;

    protected function initRegistry(): void
    {
        parent::initRegistry();
        CorePluginInitialization::initialize($this->registry);
    }
}
