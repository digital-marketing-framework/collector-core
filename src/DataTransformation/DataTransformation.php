<?php

namespace DigitalMarketingFramework\Collector\Core\DataTransformation;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareTrait;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataMapperGroupReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataPrivacyPermissionSelectionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class DataTransformation extends ConfigurablePlugin implements DataTransformationInterface, DataPrivacyManagerAwareInterface, DataProcessorAwareInterface
{
    use DataPrivacyManagerAwareTrait;
    use DataProcessorAwareTrait;

    public const KEY_VISIBILITY = 'visibility';

    public const KEY_REQUIRED_PERMISSION = 'requiredPermission';

    public const KEY_DATA_MAP = 'dataMap';

    public function __construct(
        string $keyword,
        protected CollectorConfigurationInterface $collectorConfiguration,
        protected bool $public = false
    ) {
        parent::__construct($keyword);
        if (!$collectorConfiguration->dataTransformationExists($keyword)) {
            throw new DigitalMarketingFrameworkException(sprintf('Data transformation "%s" not found', $keyword));
        }

        $this->configuration = $collectorConfiguration->getDataTransformationConfiguration($keyword);
    }

    public function getVisibility(): string
    {
        return $this->getConfig(static::KEY_VISIBILITY);
    }

    public function allowed(): bool
    {
        $permission = $this->getConfig(static::KEY_REQUIRED_PERMISSION);

        if (!$this->dataPrivacyManager->getPermission($permission)) {
            return false;
        }

        $visibility = $this->getVisibility();

        return match ($visibility) {
            static::VISIBILITY_DISABLED => false,
            static::VISIBILITY_PRIVATE => !$this->public,
            static::VISIBILITY_PUBLIC => true,
            default => throw new DigitalMarketingFrameworkException(sprintf('Unknown visibility status "%s" for data transformation "%s"', $visibility, $this->keyword)),
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
        $schema->getRenderingDefinition()->setIcon(Icon::DATA_TRANSFORMATION);

        $visibilitySchema = new StringSchema(static::DEFAULT_VISIBILITY);
        $visibilitySchema->getAllowedValues()->addValue(static::VISIBILITY_DISABLED);
        $visibilitySchema->getAllowedValues()->addValue(static::VISIBILITY_PRIVATE);
        $visibilitySchema->getAllowedValues()->addValue(static::VISIBILITY_PUBLIC);
        $visibilitySchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $schema->addProperty(static::KEY_VISIBILITY, $visibilitySchema);

        $requiredPermissionSchema = new CustomSchema(DataPrivacyPermissionSelectionSchema::TYPE);
        $schema->addProperty(static::KEY_REQUIRED_PERMISSION, $requiredPermissionSchema);

        $dataTransformationMapper = new CustomSchema(DataMapperGroupReferenceSchema::TYPE);
        $schema->addProperty(static::KEY_DATA_MAP, $dataTransformationMapper);

        return $schema;
    }
}
