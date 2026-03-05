<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifier;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class CollectorConfiguration extends Configuration implements CollectorConfigurationInterface
{
    public function getInboundConfiguration(string $integrationName): array
    {
        return $this->getIntegrationConfiguration($integrationName)[static::KEY_INBOUND_ROUTES] ?? [];
    }

    public function getGeneralInboundConfiguration(): array
    {
        return $this->getInboundConfiguration(static::KEY_GENERAL_INTEGRATION);
    }

    public function getGeneralCacheTimeoutInSeconds(): int
    {
        return $this->getGeneralInboundConfiguration()[static::KEY_CACHE_TIMEOUT] ?? static::DEFAULT_CACHE_TIMEOUT;
    }

    public function getInboundRouteConfiguration(string $integrationName, string $inboundRouteName): array
    {
        return $this->getInboundConfiguration($integrationName)[$inboundRouteName] ?? [];
    }

    public function inboundRouteExists(string $integrationName, string $inboundRouteName): bool
    {
        return isset($this->getInboundConfiguration($integrationName)[$inboundRouteName]);
    }

    public function getPersonalizationConfiguration(): array
    {
        return $this->get(static::KEY_PERSONALIZATION, []);
    }

    public function getDataTransformationConfigurationItems(): array
    {
        return $this->getPersonalizationConfiguration()[static::KEY_DATA_TRANSFORMATIONS] ?? [];
    }

    public function getDataTransformationName(string $transformationId): ?string
    {
        $transformationItem = $this->getDataTransformationConfigurationItems()[$transformationId] ?? null;
        if ($transformationItem === null) {
            return null;
        }

        return MapUtility::getItemKey($transformationItem);
    }

    public function dataTransformationExists(string $transformationName): bool
    {
        return isset(MapUtility::flatten($this->getDataTransformationConfigurationItems())[$transformationName]);
    }

    public function getDataTransformationConfiguration(string $transformationName): array
    {
        return MapUtility::flatten($this->getDataTransformationConfigurationItems())[$transformationName] ?? [];
    }

    public function getPersonaGroupConfigurationItems(): array
    {
        return $this->getPersonalizationConfiguration()[static::KEY_PERSONAS] ?? [];
    }

    public function personaGroupExists(string $personaGroupId): bool
    {
        return isset($this->getPersonaGroupConfigurationItems()[$personaGroupId]);
    }

    public function getPersonaGroupConfiguration(string $personaGroupId): array
    {
        $item = $this->getPersonaGroupConfigurationItems()[$personaGroupId] ?? null;
        if ($item === null) {
            return [];
        }

        return MapUtility::getItemValue($item);
    }

    public function getPersonaList(string $personaGroupId): array
    {
        $groupConfig = $this->getPersonaGroupConfiguration($personaGroupId);

        return MapUtility::flatten($groupConfig[static::KEY_PERSONA_LIST] ?? []);
    }

    public function getPersonaGroupDataTransformationName(string $personaGroupId): ?string
    {
        $groupConfig = $this->getPersonaGroupConfiguration($personaGroupId);
        $id = $groupConfig[ContentModifier::KEY_DATA_TRANSFORMATION_ID] ?? '';
        if ($id === '') {
            return null;
        }

        return $this->getDataTransformationName($id);
    }

    /**
     * @return array{uuid:string,key:string,weight:int,value:array{type:string,config:array<string,array<string,mixed>>}}
     */
    protected function getContentModifierMapItem(string $contentModifierId): array
    {
        $contentModifierMap = $this->getPersonalizationConfiguration()[static::KEY_CONTENT_MODIFIERS] ?? [];
        if (!isset($contentModifierMap[$contentModifierId])) {
            throw new DigitalMarketingFrameworkException(sprintf('content modifier with ID %s not found', $contentModifierId));
        }

        return $contentModifierMap[$contentModifierId];
    }

    public function getContentModifierIdFromName(string $name): ?string
    {
        $contentModifierConfigItems = $this->getPersonalizationConfiguration()[static::KEY_CONTENT_MODIFIERS] ?? [];
        foreach ($contentModifierConfigItems as $contentModifierId => $contentModifierConfigItem) {
            if (MapUtility::getItemKey($contentModifierConfigItem) === $name) {
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
        return array_keys($this->getPersonalizationConfiguration()[static::KEY_CONTENT_MODIFIERS] ?? []);
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
