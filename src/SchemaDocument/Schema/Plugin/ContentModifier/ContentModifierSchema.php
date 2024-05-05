<?php

namespace DigitalMarketingFramework\Collector\Core\SchemaDocument\Schema\Plugin\ContentModifier;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

class ContentModifierSchema extends SwitchSchema
{
    public const TYPE = 'CONTENT_MODIFIER';

    public function __construct(mixed $default = null)
    {
        parent::__construct('contentModifier', $default);
    }
}
