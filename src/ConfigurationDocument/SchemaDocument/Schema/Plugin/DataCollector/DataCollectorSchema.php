<?php

namespace DigitalMarketingFramework\Collector\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataCollector;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class DataCollectorSchema extends ContainerSchema
{
    public function addItem(string $keyword, SchemaInterface $schema): void
    {
        $this->addProperty($keyword, $schema);
    }
}
