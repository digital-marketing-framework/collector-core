<?php

namespace DigitalMarketingFramework\Collector\Core;

use DigitalMarketingFramework\Collector\Core\ConfigurationResolver\ContentResolver\MaskedContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use DigitalMarketingFramework\Core\PluginInitialization;

class CorePluginInitialization extends PluginInitialization
{
    const PLUGINS = [
        ContentResolverInterface::class => [
            MaskedContentResolver::class,
        ],
    ];
}
