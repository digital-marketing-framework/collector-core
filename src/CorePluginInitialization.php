<?php

namespace DigitalMarketingFramework\Collector\Core;

use DigitalMarketingFramework\Collector\Core\DataProcessor\ValueModifier\MaskedValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\PluginInitialization;

class CorePluginInitialization extends PluginInitialization
{
    const PLUGINS = [
        ValueModifierInterface::class => [
            MaskedValueModifier::class,
        ],
    ];
}
