<?php

namespace DigitalMarketingFramework\Collector\Core\DataTransformation;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataMapperGroupReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class DataTransformation extends ConfigurablePlugin implements DataTransformationInterface, DataProcessorAwareInterface
{
    use DataProcessorAwareTrait;

    public const KEY_VISIBILITY = 'visibility';

    public const KEY_DATA_MAP = 'dataMap';

    public function __construct(
        string $keyword,
        protected CollectorConfigurationInterface $collectorConfiguration,
        protected bool $public = false
    ) {
        parent::__construct($keyword);
        if (!$collectorConfiguration->dataTransformationExists($keyword)) {
            throw new DigitalMarketingFrameworkException(sprintf('data transformation "%s" not found', $keyword));
        }

        $this->configuration = $collectorConfiguration->getDataTransformationConfiguration($keyword);
    }

    public function getVisibility(): string
    {
        return $this->getConfig(static::KEY_VISIBILITY);
    }

    public function allowed(): bool
    {
        $visibility = $this->getVisibility();

        return match ($visibility) {
            static::VISIBILITY_DISABLED => false,
            static::VISIBILITY_PRIVATE => !$this->public,
            static::VISIBILITY_PUBLIC => true,
            default => throw new DigitalMarketingFrameworkException(sprintf('unknow visibility status "%s" for data transformation "%s"', $visibility, $this->keyword)),
        };
    }

    public function transform(DataInterface $data): DataInterface
    {
        if (!$this->allowed()) {
            return new Data();
        }

        $dataMapperGroupId = $this->getConfig(static::KEY_DATA_MAP);
        $dataMapperGroupConfig = $this->collectorConfiguration->getDataMapperGroupConfiguration($dataMapperGroupId);
        $context = $this->dataProcessor->createContext($data, $this->collectorConfiguration);

        return $this->dataProcessor->processDataMapperGroup($dataMapperGroupConfig, $context);
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setIcon('data-transformation');

        $visibility = new StringSchema(static::DEFAULT_VISIBILITY);
        $visibility->getAllowedValues()->addValue(static::VISIBILITY_DISABLED);
        $visibility->getAllowedValues()->addValue(static::VISIBILITY_PRIVATE);
        $visibility->getAllowedValues()->addValue(static::VISIBILITY_PUBLIC);
        $visibility->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $schema->addProperty(static::KEY_VISIBILITY, $visibility);

        $dataTransformationMapper = new CustomSchema(DataMapperGroupReferenceSchema::TYPE);
        $schema->addProperty(static::KEY_DATA_MAP, $dataTransformationMapper);

        return $schema;
    }
}
