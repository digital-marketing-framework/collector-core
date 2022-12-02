<?php

namespace DigitalMarketingFramework\Collector\Core;

use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryInterface;
use DigitalMarketingFramework\Collector\Core\ConfigurationResolverInitialization;

class CoreInitialization extends Initialization
{
    protected const PLUGINS = [
        DataCollectorInterface::class => [
        ],
    ];
    
    public static function initialize(PluginRegistryInterface $registry): void
    {
        ConfigurationResolverInitialization::initialize($registry);
        parent::initialize($registry);
    }
}
