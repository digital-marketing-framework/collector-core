<?php

namespace DigitalMarketingFramework\Collector\Core;

use DigitalMarketingFramework\Collector\Core\ConfigurationResolver\ContentResolver\MaskedContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolverInitialization as CoreConfigurationResolverInitialization;
use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryInterface;

class ConfigurationResolverInitialization extends Initialization
{
    const PLUGINS = [
        ContentResolverInterface::class => [
            MaskedContentResolver::class,
        ],
    ];
    
    public static function initialize(PluginRegistryInterface $registry): void
    {
        CoreConfigurationResolverInitialization::initialize($registry);
        parent::initialize($registry);
    }
}
