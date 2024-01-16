<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;

interface DataTransformationRegistryInterface
{
    public function getDataTransformation(string $keyword, CollectorConfigurationInterface $collectorConfiguration, bool $public = false): DataTransformationInterface;

    /**
     * @return array<string>
     */
    public function getDataTransformationNames(CollectorConfigurationInterface $collectorConfiguration, bool $public = false): array;

    /**
     * @return array<string>
     */
    public function getPublicDataTransformationNames(CollectorConfigurationInterface $collectorConfiguration): array;
}
