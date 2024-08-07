<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface ContentModifierInterface extends ConfigurablePluginInterface
{
    public function getContentModifierId(): string;

    public function getContentModifierName(): string;

    public function getPublicKey(EndPointInterface $endPoint): string;

    public function allowed(): bool;

    public function transformData(DataInterface $data): DataInterface;

    public function invalidIdentifierHandling(): bool;

    /**
     * @return ?array<string>
     */
    public function getRequiredFieldGroups(): ?array;

    /**
     * Schema for content specific backend settings
     */
    public function getBackendSettingsSchema(SchemaDocument $schemaDocument): SchemaInterface;

    /**
     * Backend data to produce or update markup, derived from content specific backend settings
     *
     * @param array<string,mixed> $settings
     *
     * @return array<string,mixed>
     */
    public function getBackendData(array $settings): array;

    /**
     * @return array<string>
     */
    public function getTemplateNameCandidates(): array;

    /**
     * Template variables to produce or update markup, derived from content specific backend settings
     *
     * @param array<string,mixed> $settings
     *
     * @return array<string,mixed>
     */
    public function getTemplateViewData(EndPointInterface $endPoint, array $settings): array;

    /**
     * Produce markup, derived from content specific backend settings
     *
     * @param array<string,mixed> $settings
     */
    public function render(EndPointInterface $endPoint, array $settings): ?string;
}
