<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;

interface ContentModifierInterface extends ConfigurablePluginInterface
{
    public function getContentModifierId(): string;

    public function getContentModifierName(): string;

    public function transformData(DataInterface $data): DataInterface;

    public function invalidIdentifierHandling(): bool;

    /**
     * @return ?array<string>
     */
    public function getRequiredFieldGroups(): ?array;
}
