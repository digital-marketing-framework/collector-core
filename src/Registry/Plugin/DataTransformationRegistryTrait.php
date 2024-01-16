<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformation;
use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;
use DigitalMarketingFramework\Core\Utility\MapUtility;

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

    public function getDataTransformationNames(CollectorConfigurationInterface $collectorConfiguration, bool $public = false): array
    {
        $result = [];
        $items = $collectorConfiguration->getDataTransformationConfigurationItems();
        foreach ($items as $item) {
            $keyword = MapUtility::getItemKey($item);
            $transformation = $this->getDataTransformation($keyword, $collectorConfiguration, $public);
            if ($transformation->allowed()) {
                $result[] = $keyword;
            }
        }

        return $result;
    }

    public function getPublicDataTransformationNames(CollectorConfigurationInterface $collectorConfiguration): array
    {
        return $this->getDataTransformationNames($collectorConfiguration, true);
    }
}
