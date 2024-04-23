<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface CollectorConfigurationInterface extends ConfigurationInterface
{
    public const KEY_INBOUND_ROUTES = 'inboundRoutes';

    public const KEY_PERSONALIZATION = 'personalization';

    public const KEY_DATA_TRANSFORMATIONS = 'dataTransformations';

    public const KEY_DEFAULT_DATA_TRANSFORMATION = 'defaultTransformation';

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

    public function dataTransformationExists(string $transformatioName): bool;

    /**
     * @return array<string,mixed>
     */
    public function getDataTransformationConfiguration(string $transformationName): array;

    public function getDefaultDataTransformationName(): string;

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
