<?php

namespace DigitalMarketingFramework\Collector\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataCollector;

use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\PluginSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class DataCollectorSchema extends PluginSchema
{
    public function addDataCollector(string $keyword, SchemaInterface $schema): void
    {
        $this->addProperty($keyword, $schema);
    }

    protected function getPluginInterface(): string
    {
        return DataCollectorInterface::class;
    }

    protected function processPlugin(string $keyword, string $class): void
    {
        $this->addDataCollector($keyword, $class::getSchema());
    }
}
