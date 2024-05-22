<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Collector\Core\Route\InboundRouteInterface;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\Schema\Custom\DataTransformationReferenceSchema;
use DigitalMarketingFramework\Collector\Core\Service\CollectorAwareInterface;
use DigitalMarketingFramework\Collector\Core\Service\CollectorAwareTrait;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class ContentModifier extends ConfigurablePlugin implements ContentModifierInterface, DataProcessorAwareInterface
{
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

    public function getPublicKey(EndPointInterface $endPoint): string
    {
        return implode(':', [
            'collector',
            'contentModifiers',
            $this->getKeyword(),
            $endPoint->getName(),
            $this->getContentModifierName(),
        ]);
    }

    protected function getDataProcessorContext(DataInterface $data): DataProcessorContextInterface
    {
        return $this->dataProcessor->createContext($data, $this->collectorConfiguration);
    }

    protected function dataTransformationMustBePublic(): bool
    {
        return false;
    }

    public function transformData(DataInterface $data): DataInterface
    {
        $id = $this->getConfig(static::KEY_DATA_TRANSFORMATION_ID);
        $name = $id === '' ? null : $this->collectorConfiguration->getDataTransformationName($id);

        if ($name === null) {
            if ($this->dataTransformationMustBePublic()) {
                throw new DigitalMarketingFrameworkException('No data transformation given for content modifier');
            }

            return $data;
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

    public function invalidIdentifierHandling(): bool
    {
        return true;
    }

    public function getRequiredFieldGroups(): ?array
    {
        return [InboundRouteInterface::STANDARD_FIELD_GROUP];
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setIcon('content-modifier');

        $transformationSchema = new DataTransformationReferenceSchema(required: false, firstEmptyOptionLabel: '[Passthrough]');
        $transformationSchema->getRenderingDefinition()->setLabel('Preprocessing Data Transformation');
        $schema->addProperty(static::KEY_DATA_TRANSFORMATION_ID, $transformationSchema);

        return $schema;
    }

    public function getBackendSettingsSchema(): SchemaInterface
    {
        return new ContainerSchema();
    }

    public function getBackendData(array $settings): array
    {
        return [];
    }
}
