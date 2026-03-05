<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface CollectorConfigurationInterface extends ConfigurationInterface
{
    public const KEY_INBOUND_ROUTES = 'inboundRoutes';

    public const KEY_PERSONALIZATION = 'personalization';

    public const KEY_DATA_TRANSFORMATIONS = 'dataTransformations';

    public const KEY_PERSONAS = 'personas';

    public const KEY_PERSONA_LIST = 'personaList';

    public const KEY_CONTENT_MODIFIERS = 'contentModifiers';

    public const KEY_CACHE_TIMEOUT = 'cacheTimeoutInSeconds';

    public const DEFAULT_CACHE_TIMEOUT = 1000;

    /**
     * @return array<string,mixed>
     */
    public function getInboundConfiguration(string $integrationName): array;

    /**
     * @return array<string,mixed>
     */
    public function getGeneralInboundConfiguration(): array;

    public function getGeneralCacheTimeoutInSeconds(): int;

    public function inboundRouteExists(string $integrationName, string $inboundRouteName): bool;

    /**
     * @return array<string,mixed>
     */
    public function getInboundRouteConfiguration(string $integrationName, string $inboundRouteName): array;

    /**
     * @return array<string,mixed>
     */
    public function getPersonalizationConfiguration(): array;

    /**
     * @return array<string,array{uuid:string,key:string,weight:int,value:array<string,mixed>}>
     */
    public function getDataTransformationConfigurationItems(): array;

    public function getDataTransformationName(string $transformationId): ?string;

    public function dataTransformationExists(string $transformationName): bool;

    /**
     * @return array<string,mixed>
     */
    public function getDataTransformationConfiguration(string $transformationName): array;

    /**
     * @return array<string,array{uuid:string,key:string,weight:int,value:array<string,mixed>}>
     */
    public function getPersonaGroupConfigurationItems(): array;

    public function personaGroupExists(string $personaGroupId): bool;

    /**
     * @return array<string,mixed>
     */
    public function getPersonaGroupConfiguration(string $personaGroupId): array;

    /**
     * Flattened persona map for a group: name => condition config
     *
     * @return array<string,mixed>
     */
    public function getPersonaList(string $personaGroupId): array;

    public function getPersonaGroupDataTransformationName(string $personaGroupId): ?string;

    public function getContentModifierIdFromName(string $name): ?string;

    /**
     * @return array<string>
     */
    public function getContentModifierIds(): array;

    public function getContentModifierName(string $contentModifierId): string;

    /**
     * @return array<string,mixed>
     */
    public function getContentModifierConfiguration(string $contentModifierId): array;

    public function getContentModifierKeyword(string $contentModifierId): string;
}
