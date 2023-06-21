<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;

interface DataTransformationRegistryInterface
{
    public function getDataTransformation(string $keyword, CollectorConfigurationInterface $collectorConfiguration, bool $public = false);
}
