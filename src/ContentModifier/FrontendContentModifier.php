<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Collector\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class FrontendContentModifier extends ContentModifier implements FrontendContentModifierInterface
{
    abstract public function getFrontendData(DataInterface $data, array $arguments): array|false;

    public function getFrontendSettings(): array
    {
        return [];
    }

    public function getContentSpecificFrontendSettings(string $id, array $settings): array
    {
        return $settings;
    }

    public function activateFrontendScripts(): void
    {
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setIcon(Icon::FRONTEND_CONTENT_MODIFIER);

        return $schema;
    }
}
