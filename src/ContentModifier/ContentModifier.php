<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Collector\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\DataTransformationReferenceSchema;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Collector\Core\Service\CollectorAwareInterface;
use DigitalMarketingFramework\Collector\Core\Service\CollectorAwareTrait;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

abstract class ContentModifier extends ConfigurablePlugin implements ContentModifierInterface, CollectorAwareInterface, DataProcessorAwareInterface
{
    use CollectorAwareTrait;
    use DataProcessorAwareTrait;

    public const KEY_DATA_TRANSFORMATION_ID = 'dataTransformationId';

    protected DataInterface $data;

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected CollectorConfigurationInterface $collectorConfiguration,
    ) {
        parent::__construct($keyword, $registry);
        $this->configuration = $collectorConfiguration->getContentModifierConfiguration($this->getKeyword());
    }

    protected function getDataProcessorContext(): DataProcessorContextInterface
    {
        return $this->dataProcessor->createContext($this->getData(), $this->collectorConfiguration);
    }

    protected function publicTransformation(): bool
    {
        return false;
    }

    protected function transformData(DataInterface $data): DataInterface
    {
        $id = $this->getConfig(static::KEY_DATA_TRANSFORMATION_ID);
        if ($id === '') {
            return $data;
        }

        $name = $this->collectorConfiguration->getDataTransformationName($id);
        if ($name === null) {
            return $data;
        }

        $transformation = $this->registry->getDataTransformation($name, $this->collectorConfiguration, $this->publicTransformation());
        if ($transformation->allowed()) {
            $data = $transformation->transform($data);
        }

        return $data;
    }

    protected function invalidIdentifierHandling(): bool
    {
        return true;
    }

    protected function getData(): DataInterface
    {
        if (!isset($this->data)) {
            $this->data = $this->collector->collect($this->collectorConfiguration, invalidIdentifierHandling: $this->invalidIdentifierHandling());
            $this->data = $this->transformData($this->data);
        }

        return $this->data;
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();

        $transformationSchema = new DataTransformationReferenceSchema(required: false);
        $schema->addProperty(static::KEY_DATA_TRANSFORMATION_ID, $transformationSchema);

        return $schema;
    }
}
