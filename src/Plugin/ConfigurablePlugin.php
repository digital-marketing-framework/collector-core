<?php

namespace DigitalMarketingFramework\Collector\Core\Plugin;

use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePlugin as CoreConfigurablePlugin;

abstract class ConfigurablePlugin extends CoreConfigurablePlugin
{
    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
    ) {
        parent::__construct($keyword);
    }
}
