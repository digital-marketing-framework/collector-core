<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface ContentModifierInterface extends ConfigurablePluginInterface
{
    public function getContentModifierId(): string;

    public function getContentModifierName(): string;

    public function getPublicKey(EndPointInterface $endPoint): string;

    public function transformData(DataInterface $data): DataInterface;

    public function invalidIdentifierHandling(): bool;

    /**
     * @return ?array<string>
     */
    public function getRequiredFieldGroups(): ?array;

    public function getBackendSettingsSchema(): SchemaInterface;

    /**
     * @param array<string,mixed> $settings
     * @return array<string,mixed>
     */
    public function getBackendData(array $settings): array;
}
