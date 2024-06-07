<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Collector\Core\Route\InboundRouteInterface;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\Schema\Custom\DataTransformationReferenceSchema;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareTrait;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataPrivacyPermissionSelectionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareTrait;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use DigitalMarketingFramework\TemplateEngineTwig\TemplateEngine\TwigTemplateEngine;

abstract class ContentModifier extends ConfigurablePlugin implements ContentModifierInterface, LoggerAwareInterface, DataProcessorAwareInterface, TemplateEngineAwareInterface, DataPrivacyManagerAwareInterface
{
    use LoggerAwareTrait;
    use DataProcessorAwareTrait;
    use TemplateEngineAwareTrait;
    use DataPrivacyManagerAwareTrait;

    protected const KEY_REQUIRED_PERMISSION = 'requiredPermission';

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

    public function allowed(): bool
    {
        $permission = $this->getConfig(static::KEY_REQUIRED_PERMISSION);

        return $this->dataPrivacyManager->getPermission($permission);
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
        if (!$this->allowed()) {
            $this->logger->warning(sprintf('Content modifier %s denied due to user permission and still requested', $this->getKeyword()));

            return new Data();
        }

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

        $requiredPermissionSchema = new CustomSchema(DataPrivacyPermissionSelectionSchema::TYPE);
        $schema->addProperty(static::KEY_REQUIRED_PERMISSION, $requiredPermissionSchema);

        $transformationSchema = new DataTransformationReferenceSchema(required: false, firstEmptyOptionLabel: '[Passthrough]');
        $transformationSchema->getRenderingDefinition()->setLabel('Preprocessing Data Transformation');
        $schema->addProperty(static::KEY_DATA_TRANSFORMATION_ID, $transformationSchema);

        return $schema;
    }

    public function getBackendSettingsSchema(SchemaDocument $schemaDocument): SchemaInterface
    {
        return new ContainerSchema();
    }

    public function getBackendData(array $settings): array
    {
        return [];
    }

    public function getTemplateNameCandidates(): array
    {
        $pluginType = GeneralUtility::camelCaseToDashed($this->getKeyword());
        $pluginName = GeneralUtility::camelCaseToDashed($this->getContentModifierName());

        return [
            'content-modifiers/' . $pluginType . '-' . $pluginName . '.html.twig',
            'content-modifiers/' . $pluginType . '.html.twig',
        ];
    }

    public function getTemplateViewData(EndPointInterface $endPoint, array $settings): array
    {
        return [
            'publicKey' => $this->getPublicKey($endPoint),
            'pluginType' => ucfirst($this->getKeyword()),
            'pluginName' => ucfirst($this->getContentModifierName()),
            'data' => $this->getBackendData($settings),
        ];
    }

    public function render(EndPointInterface $endPoint, array $settings): ?string
    {
        $viewData = $this->getTemplateViewData($endPoint, $settings);
        $templateNameCandidates = $this->getTemplateNameCandidates();

        $config = [
            TwigTemplateEngine::KEY_TEMPLATE => '',
            TwigTemplateEngine::KEY_TEMPLATE_NAME => $templateNameCandidates,
        ];

        $result = $this->templateEngine->render($config, $viewData);

        if ($result !== '') {
            return $result;
        }

        return null;
    }
}
