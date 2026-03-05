<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifier;
use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierHandlerAwareInterface;
use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformation;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\ContentModifierRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataTransformationRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\InboundRouteRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\CollectorApiRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\CollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\ContentModifierHandlerRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\InvalidIdentifierHandlerRegistryTrait;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\Schema\Custom\DataTransformationReferenceSchema;
use DigitalMarketingFramework\Collector\Core\Service\CollectorAwareInterface;
use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ConditionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class Registry extends CoreRegistry implements RegistryInterface
{
    use InvalidIdentifierHandlerRegistryTrait;
    use InboundRouteRegistryTrait;
    use CollectorRegistryTrait;
    use DataTransformationRegistryTrait;
    use ContentModifierRegistryTrait;
    use CollectorApiRegistryTrait;
    use ContentModifierHandlerRegistryTrait;

    public function processObjectAwareness(object $object): void
    {
        parent::processObjectAwareness($object);

        if ($object instanceof CollectorAwareInterface) {
            $object->setCollector($this->getCollector());
        }

        if ($object instanceof ContentModifierHandlerAwareInterface) {
            $object->setContentModifierHandler($this->getContentModifierHandler());
        }
    }

    protected function getDataTransformationSchema(): SchemaInterface
    {
        $transformationName = new StringSchema('transformationName');
        $transformation = DataTransformation::getSchema();

        $transformationsSchema = new MapSchema($transformation, $transformationName);
        $transformationsSchema->getRenderingDefinition()->setIcon(Icon::DATA_TRANSFORMATIONS);

        return $transformationsSchema;
    }

    public function addConfigurationSchemaDocument(SchemaDocument $schemaDocument): void
    {
        parent::addConfigurationSchemaDocument($schemaDocument);

        $generalInboundConfiguration = new ContainerSchema();
        $generalInboundConfiguration->getRenderingDefinition()->setIcon(Icon::INBOUND_ROUTES);
        $cacheTimeoutSchema = new IntegerSchema(CollectorConfigurationInterface::DEFAULT_CACHE_TIMEOUT);
        $cacheTimeoutSchema->getRenderingDefinition()->setLabel('Cache lifetime (seconds)');
        $generalInboundConfiguration->addProperty(CollectorConfigurationInterface::KEY_CACHE_TIMEOUT, $cacheTimeoutSchema);
        $generalIntegration = $this->getGeneralIntegrationSchema($schemaDocument);
        $generalIntegration->addProperty(CollectorConfigurationInterface::KEY_INBOUND_ROUTES, $generalInboundConfiguration);

        $this->addInboundRoutesSchemas($schemaDocument);

        $personalizationSchema = new ContainerSchema();
        $personalizationSchema->getRenderingDefinition()->setIcon(Icon::PERSONALIZATION);

        $personalizationSchema->addProperty(
            CollectorConfigurationInterface::KEY_DATA_TRANSFORMATIONS,
            $this->getDataTransformationSchema()
        );

        $personaGroupSchema = new ContainerSchema();

        $personaGroupTransformationSchema = new DataTransformationReferenceSchema(
            required: false,
            firstEmptyOptionLabel: '[Passthrough]'
        );
        $personaGroupSchema->addProperty(
            ContentModifier::KEY_DATA_TRANSFORMATION_ID,
            $personaGroupTransformationSchema
        );

        $personaListSchema = new MapSchema(
            new CustomSchema(ConditionSchema::TYPE),
            new StringSchema()
        );
        $personaListSchema->getRenderingDefinition()->setSkipInNavigation(true);
        $personaGroupSchema->addProperty(
            CollectorConfigurationInterface::KEY_PERSONA_LIST,
            $personaListSchema
        );

        $personasSchema = new MapSchema($personaGroupSchema, new StringSchema('groupName'));
        $personasSchema->getRenderingDefinition()->setIcon(Icon::PERSONALIZATION);

        $personalizationSchema->addProperty(
            CollectorConfigurationInterface::KEY_PERSONAS,
            $personasSchema
        );

        $personalizationSchema->addProperty(
            CollectorConfigurationInterface::KEY_CONTENT_MODIFIERS,
            $this->getContentModifiersSchema($schemaDocument)
        );

        $schemaDocument->getMainSchema()->addProperty(
            CollectorConfigurationInterface::KEY_PERSONALIZATION,
            $personalizationSchema
        );
    }
}
