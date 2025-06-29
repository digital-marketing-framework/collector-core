<?php

namespace DigitalMarketingFramework\Collector\Core\InvalidIdentifier;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class InvalidIdentifierSchema extends ContainerSchema
{
    public const KEY_TSTAMP = 'tstamp';

    public const DEFAULT_TSTAMP = 0;

    public const KEY_IDENTIFIER = 'identifier';

    public const DEFAULT_IDENTIFIER = '';

    public const KEY_COUNT = 'count';

    public const DEFAULT_COUNT = 0;

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        // TODO render format should be a date/time string
        $this->addProperty(static::KEY_TSTAMP, new IntegerSchema(static::DEFAULT_TSTAMP));

        $this->addProperty(static::KEY_IDENTIFIER, new StringSchema(static::DEFAULT_IDENTIFIER));

        $this->addProperty(static::KEY_COUNT, new IntegerSchema(static::DEFAULT_COUNT));
    }
}
