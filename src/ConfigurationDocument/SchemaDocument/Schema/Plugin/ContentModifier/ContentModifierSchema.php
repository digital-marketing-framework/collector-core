<?php

namespace DigitalMarketingFramework\Collector\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\ContentModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class ContentModifierSchema extends SwitchSchema
{
    public const TYPE = 'CONTENT_MODIFIER';

    public function __construct(mixed $default = null)
    {
        parent::__construct('contentModifier', $default);
    }
}
