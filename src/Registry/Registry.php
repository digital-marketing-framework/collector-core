<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\DataTransformation\DataTransformation;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\ContentModifierRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataTransformationRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\InboundRouteRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\CollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\InvalidIdentifierHandlerRegistryTrait;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\Schema\Custom\DataTransformationReferenceSchema;
use DigitalMarketingFramework\Collector\Core\Service\CollectorAwareInterface;
use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
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

        return new MapSchema($transformation, $transformationName);
    }

    public function addConfigurationSchema(SchemaDocument $schemaDocument): void
    {
        parent::addConfigurationSchema($schemaDocument);

        $generalInboundConfiguration = new ContainerSchema();
        $generalInboundConfiguration->getRenderingDefinition()->setIcon('inbound-routes');
        $cacheTimeoutSchema = new IntegerSchema(CollectorConfigurationInterface::DEFAULT_CACHE_TIMEOUT);
        $cacheTimeoutSchema->getRenderingDefinition()->setLabel('Cache lifetime (seconds)');
        $generalInboundConfiguration->addProperty(CollectorConfigurationInterface::KEY_CACHE_TIMEOUT, $cacheTimeoutSchema);
        $generalIntegration = $this->getGeneralIntegrationSchema($schemaDocument);
        $generalIntegration->addProperty(CollectorConfigurationInterface::KEY_INBOUND_ROUTES, $generalInboundConfiguration);

        $this->addInboundRoutesSchemas($schemaDocument);

        $personalizationSchema = new ContainerSchema();

        $personalizationSchema->addProperty(
            CollectorConfigurationInterface::KEY_DEFAULT_DATA_TRANSFORMATION,
            new DataTransformationReferenceSchema(
                required: false,
                firstEmptyOptionLabel: '[None]'
            )
        );

        $personalizationSchema->addProperty(
            CollectorConfigurationInterface::KEY_DATA_TRANSFORMATIONS,
            $this->getDataTransformationSchema()
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
