<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataCollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\InvalidIdentifierHandlerRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Service\Collector;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;

class Registry extends CoreRegistry implements RegistryInterface
{
    use InvalidIdentifierHandlerRegistryTrait;
    use DataCollectorRegistryTrait;

    protected CollectorInterface $collector;

    public function getCollector(): CollectorInterface
    {
        if (!isset($this->collector)) {
             $this->collector = $this->createObject(Collector::class, [$this]);
        }
        return $this->collector;
    }

    public function getCollectorDefaultConfiguration(): array
    {
        $defaultCollectorConfiguration = Collector::getDefaultConfiguration();
        $defaultCollectorConfiguration[CollectorConfigurationInterface::KEY_DATA_COLLECTORS] = $this->getDataCollectorDefaultConfigurations();
        return $defaultCollectorConfiguration;
    }

    public function addConfigurationSchema(SchemaDocument $schemaDocument): void
    {
        parent::addConfigurationSchema($schemaDocument);
        $collectorSchema = new ContainerSchema();
        $collectorSchema->addProperty(CollectorConfiguration::KEY_DATA_COLLECTORS, $this->getDataCollectorSchema());

        $schemaDocument->getMainSchema()->addProperty(CollectorConfiguration::KEY_COLLECTOR, $collectorSchema);
    }
}
