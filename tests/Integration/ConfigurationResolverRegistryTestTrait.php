<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration;

use DigitalMarketingFramework\Collector\Core\ConfigurationResolverInitialization;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;

trait ConfigurationResolverRegistryTestTrait // extends \DigitalMarketingFramework\Core\Tests\Integration\RegistryTestTrait
{
    protected ConfigurationResolverRegistryInterface $registry;

    protected function initRegistry(): void
    {
        parent::initRegistry();
        ConfigurationResolverInitialization::initialize($this->registry);
    }
}
