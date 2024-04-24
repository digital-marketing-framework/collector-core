<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Route\InboundRouteInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldListDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

trait InboundRouteRegistryTrait
{
    use PluginRegistryTrait;

    public function registerInboundRoute(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(InboundRouteInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteInboundRoute(string $keyword): void
    {
        $this->deletePlugin($keyword, InboundRouteInterface::class);
    }

    public function getInboundRoute(string $keyword, ConfigurationInterface $configuration): ?InboundRouteInterface
    {
        return $this->getPlugin($keyword, InboundRouteInterface::class, [CollectorConfiguration::convert($configuration)]);
    }

    /**
     * @return array<InboundRouteInterface>
     */
    public function getAllInboundRoutes(ConfigurationInterface $configuration): array
    {
        return $this->getAllPlugins(InboundRouteInterface::class, [$configuration]);
    }

    protected function addInboundRoutesSchemas(SchemaDocument $schemaDocument): void
    {
        foreach ($this->getAllPluginClasses(InboundRouteInterface::class) as $keyword => $class) {
            $schema = $class::getSchema();
            $integration = $class::getIntegrationName();
            $integrationLabel = $class::getIntegrationLabel();
            $label = $class::getLabel();
            $inboundRouteListLabel = $class::getInboundRouteListLabel();

            $fields = $class::getDefaultFields();
            if ($fields !== []) {
                $fieldListDefinition = new FieldListDefinition(sprintf('collector.in.defaults.%s.%s', $integration, $keyword));
                foreach ($fields as $field) {
                    if (!$field instanceof FieldDefinition) {
                        $field = new FieldDefinition($field);
                    }

                    $fieldListDefinition->addField($field);
                }

                $schemaDocument->addFieldContext($fieldListDefinition->getName(), $fieldListDefinition);
            }

            $schemaDocument->addValueToValueSet('inboundRoutes/all', $keyword);
            $schemaDocument->addValueToValueSet('inboundRoutes/' . $integration . '/all', $keyword);

            $integrationSchema = $this->getIntegrationSchemaForPluginClass($schemaDocument, $class);
            $integrationInboundSchema = $integrationSchema->getProperty(CollectorConfigurationInterface::KEY_INBOUND_ROUTES);
            if (!$integrationInboundSchema instanceof ContainerSchema) {
                $integrationInboundSchema = new ContainerSchema();
                if ($inboundRouteListLabel === null) {
                    $inboundRouteListLabel = 'Routes from ' . ($integrationLabel ?? GeneralUtility::getLabelFromValue($integration));
                }
                $integrationInboundSchema->getRenderingDefinition()->setLabel($inboundRouteListLabel);
                $integrationInboundSchema->getRenderingDefinition()->setIcon('inbound-routes');
                $integrationSchema->addProperty(CollectorConfigurationInterface::KEY_INBOUND_ROUTES, $integrationInboundSchema);
            }
            $property = $integrationInboundSchema->addProperty($keyword, $schema);
            $property->getRenderingDefinition()->setLabel($label);
        }
    }
}
