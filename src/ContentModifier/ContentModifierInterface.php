<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;

interface ContentModifierInterface extends ConfigurablePluginInterface
{
    public function getContentModifierId(): string;

    public function getContentModifierName(): string;
}
