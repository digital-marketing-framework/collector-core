<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformation;
use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;

trait DataTransformationRegistryTrait
{
    use PluginRegistryTrait;

    abstract protected function createObject(string $class, array $arguments = []): object;

    public function getDataTransformation(string $keyword, CollectorConfigurationInterface $collectorConfiguration, bool $public = false): DataTransformationInterface
    {
        $transformation = $this->createObject(DataTransformation::class, [$keyword, $collectorConfiguration, $public]);
        $this->processPluginAwareness($transformation);

        return $transformation;
    }
}
