<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class CollectorConfiguration extends Configuration implements CollectorConfigurationInterface
{
    public function getCollectorConfiguration(bool $resolveNull = true): array
    {
        return $this->getMergedConfiguration($resolveNull)[static::KEY_COLLECTOR] ?? [];
    }

    public function getDataCollectorConfiguration(string $dataCollectorName): array
    {
        return $this->getCollectorConfiguration()[static::KEY_DATA_COLLECTORS][$dataCollectorName] ?? [];
    }

    public function dataCollectorExists(string $dataCollectorName): bool
    {
        return isset($this->getCollectorConfiguration()[static::KEY_DATA_COLLECTORS][$dataCollectorName]);
    }

    public function dataTransformationExists(string $transformationName): bool
    {
        return isset(MapUtility::flatten($this->getCollectorConfiguration()[static::KEY_DATA_TRANSFORMATIONS])[$transformationName]);
    }

    public function getDataTransformationConfiguration(string $transformationName): array
    {
        return MapUtility::flatten($this->getCollectorConfiguration()[static::KEY_DATA_TRANSFORMATIONS])[$transformationName] ?? [];
    }

    public function getDefaultDataTransformationName(): string
    {
        return $this->getCollectorConfiguration()[static::KEY_DEFAULT_DATA_TRANSFORMATION] ?? '';
    }
}
