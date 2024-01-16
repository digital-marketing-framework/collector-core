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
        protected string $contentModifierId,
        protected string $contentModifierName,
    ) {
        parent::__construct($keyword, $registry);
        $this->configuration = $collectorConfiguration->getContentModifierConfiguration($this->contentModifierId);
    }

    public function getContentModifierId(): string
    {
        return $this->contentModifierId;
    }

    public function getContentModifierName(): string
    {
        return $this->contentModifierName;
    }

    protected function getDataProcessorContext(): DataProcessorContextInterface
    {
        return $this->dataProcessor->createContext($this->getData(), $this->collectorConfiguration);
    }

    protected function dataTransformationMustBePublic(): bool
    {
        return false;
    }

    protected function transformData(DataInterface $data): DataInterface
    {
        $id = $this->getConfig(static::KEY_DATA_TRANSFORMATION_ID);
        $name = $id === '' ? null : $this->collectorConfiguration->getDataTransformationName($id);

        if ($name === null) {
            if ($this->dataTransformationMustBePublic()) {
                throw new DigitalMarketingException('No data transformation given for content modifier');
            } else {
            return $data;
        }
        }

        $transformation = $this->registry->getDataTransformation(
            $name,
            $this->collectorConfiguration,
            $this->dataTransformationMustBePublic()
        );
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

        $transformationSchema = new DataTransformationReferenceSchema(required: false, firstEmptyOptionLabel: '[Passthrough]');
        $transformationSchema->getRenderingDefinition()->setLabel('Preprocessing Data Transformation');
        $schema->addProperty(static::KEY_DATA_TRANSFORMATION_ID, $transformationSchema);

        return $schema;
    }
}
