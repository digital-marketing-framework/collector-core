<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface ContentModifierHandlerInterface
{
    /**
     * @return array<string,array<string,array<array<string,mixed>>>>
     */
    public function getContentSpecificSettings(): array;

    public function getContentModifierBackendSettingsSchemaDocument(bool $asList, string $contentModifierInterface): SchemaDocument;

    public function setContentSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): void;

    public function setElementSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): void;

    public function setPageSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList): void;

    public function setFormSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): void;

    /**
     * @param array{settings?:array<string,mixed>} $settings
     *
     * @return array{endPoint:EndPointInterface,contentModifier:FrontendContentModifierInterface,settings:array<string,mixed>}
     */
    public function getContentModifierWithContentSpecificBackendSettings(array $settings): array;

    /**
     * @param array{}|array{settings:array<string,array{uuid:string,weight:int,value:mixed}>} $settings
     *
     * @return array<array{endPoint:EndPointInterface,contentModifier:FrontendContentModifierInterface,settings:array<string,mixed>}>
     */
    public function getContentModifierListWithContentSpecificBackendSettings(array $settings): array;

    /**
     * @return array<string,mixed>
     */
    public function getContentSpecificFrontendSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): array;

    /**
     * @return array<string,mixed>
     */
    public function getDataAttributesFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): array;

    public function renderFromConfigurationDocument(string $configurationDocument, bool $asList, ?string $id = null): string;
}
