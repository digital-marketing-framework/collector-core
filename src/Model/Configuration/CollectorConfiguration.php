<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;
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
    /**
     * @return array{uuid:string,weight:int,value:array{type:string,config:array<string,array<string,mixed>>}}
     */
    protected function getContentModifierMapItem(string $contentModifierId): array
    {
        $contentModifierMap = $this->getCollectorConfiguration()[static::KEY_CONTENT_MODIFIERS] ?? [];
        if (!isset($contentModifierMap[$contentModifierId])) {
            throw new DigitalMarketingFrameworkException(sprintf('content modifier with ID %s not found', $contentModifierId));
        }

        return $contentModifierMap[$contentModifierId];
    }

    public function getContentModifierIdFromName(string $name): ?string
    {
        $contentModifierConfigItems = $this->getCollectorConfiguration()[static::KEY_CONTENT_MODIFIERS] ?? [];
        foreach ($contentModifierConfigItems as $contentModifierId => $contentModifierConfigItem) {
            if (MapUtility::getItemKey($contentModiifierConfigItem) === $name) {
                return $contentModifierId;
            }
        }
        return null;
    }

    /**
     * @return array<string>
     */
    public function getContentModifierIds(): array
    {
        return array_keys($this->getCollectorConfiguration()[static::KEY_CONTENT_MODIFIERS] ?? []);
    }

    public function getContentModifierName(string $contentModifierId): string
    {
        $contentModifierItem = $this->getContentModifierMapItem($contentModifierId);
        return MapUtility::getItemKey($contentModifierItem);
    }

    /**
     * @return array<string,mixed>
     */
    public function getContentModifierConfiguration(string $contentModifierId): array
    {
        $contentModifierItem = $this->getContentModifierMapItem($contentModifierId);
        $contentModifierConfiguration = MapUtility::getItemValue($contentModifierItem);

        return SwitchSchema::getSwitchConfiguration($contentModifierConfiguration);
    }

    public function getContentModifierKeyword(string $contentModifierId): string
    {
        $contentModifierItem = $this->getContentModifierMapItem($contentModifierId);
        $contentModifierConfiguration = MapUtility::getItemValue($contentModifierItem);

        return SwitchSchema::getSwitchType($contentModifierConfiguration);
    }
}
