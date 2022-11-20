<?php

namespace DigitalMarketingFramework\Collector\Core\Plugin;

use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin as CorePlugin;

class Plugin extends CorePlugin
{
    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
    ) {
        parent::__construct($keyword);
    }
}
