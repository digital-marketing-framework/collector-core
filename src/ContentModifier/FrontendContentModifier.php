<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class FrontendContentModifier extends ContentModifier implements FrontendContentModifierInterface
{
    abstract public function getFrontendData(): array|false;

    public function getFrontendSettings(): array
    {
        return [];
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setIcon('frontend-content-modifier');
        return $schema;
    }
}
