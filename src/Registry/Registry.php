<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformation;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataCollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataTransformationRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\CollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\InvalidIdentifierHandlerRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Service\CollectorAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;

class Registry extends CoreRegistry implements RegistryInterface
{
    use InvalidIdentifierHandlerRegistryTrait;
    use DataCollectorRegistryTrait;
    use CollectorRegistryTrait;
    use DataTransformationRegistryTrait;

    protected function processObjectAwareness(object $object): void
    {
        parent::processObjectAwareness($object);
        if ($object instanceof CollectorAwareInterface) {
            $object->setCollector($this->getCollector());
        }
    }

    protected function getDataTransformationSchema(): SchemaInterface
    {
        $transformationName = new StringSchema('transformationName');
        $transformation = DataTransformation::getSchema();
        $dataTransformations = new MapSchema($transformation, $transformationName);
        return $dataTransformations;
    }

    public function addConfigurationSchema(SchemaDocument $schemaDocument): void
    {
        parent::addConfigurationSchema($schemaDocument);
        $collectorSchema = new ContainerSchema();
        $collectorSchema->addProperty(CollectorConfigurationInterface::KEY_DATA_COLLECTORS, $this->getDataCollectorSchema());

        $dataTransformations = $this->getDataTransformationSchema();
        $collectorSchema->addProperty(CollectorConfigurationInterface::KEY_DATA_TRANSFORMATIONS, $dataTransformations);

        $schemaDocument->getMainSchema()->addProperty(CollectorConfigurationInterface::KEY_COLLECTOR, $collectorSchema);
    }
}
