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

    public function getDataTransformationName(string $transformationId): ?string
    {
        return $this->getCollectorConfiguration()[static::KEY_DATA_TRANSFORMATIONS][$transformationId][MapUtility::KEY_KEY] ?? null;
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
        $defaultTransformationId = $this->getCollectorConfiguration()[static::KEY_DEFAULT_DATA_TRANSFORMATION] ?? '';
        if ($defaultTransformationId !== null) {
            return $this->getDataTransformationName($defaultTransformationId) ?? '';
        }

        return '';
    }

    public function contentModifierExists(string $keyword): bool
    {
        return isset($this->getCollectorConfiguration()[static::KEY_CONTENT_MODIFIERS][$keyword]);
    }

    /**
     * @return array<string,mixed>
     */
    public function getContentModifierConfiguration(string $keyword): array
    {
        return $this->getCollectorConfiguration()[static::KEY_CONTENT_MODIFIERS][$keyword] ?? [];
    }
}
