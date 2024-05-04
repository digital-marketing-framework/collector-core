<?php

namespace DigitalMarketingFramework\Collector\Core\Route;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Result\InboundRouteResultInterface;
use DigitalMarketingFramework\Collector\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\Integration\IntegrationInfo;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataMapperGroupReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\InheritableIntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class InboundRoute extends ConfigurablePlugin implements InboundRouteInterface, DataProcessorAwareInterface
{
    use DataProcessorAwareTrait;

    protected const KEY_ENABLED = 'enabled';

    protected const DEFAULT_ENABLED = false;

    protected const KEY_PRIORITY = 'priority';

    protected const DEFAULT_PRIORITY = 10;

    public const KEY_CACHE_TIMEOUT_IN_SECONDS = 'cacheLifetime';

    protected const KEY_DATA_MAP = 'dataMap';

    protected IntegrationInfo $integrationInfo;

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected CollectorConfigurationInterface $collectorConfiguration,
        ?IntegrationInfo $integrationInfo = null,
    ) {
        parent::__construct($keyword, $registry);
        $this->integrationInfo = $integrationInfo ?? static::getDefaultIntegrationInfo();
        $this->configuration = $collectorConfiguration->getInboundRouteConfiguration($this->integrationInfo->getName(), $this->getKeyword());
    }

    abstract public static function getDefaultIntegrationInfo(): IntegrationInfo;

    public function getIntegrationInfo(): IntegrationInfo
    {
        return $this->integrationInfo;
    }

    public function getProvidedFieldGroups(): array
    {
        return [static::STANDARD_FIELD_GROUP];
    }

    protected function proceed(): bool
    {
        return (bool)$this->getConfig(static::KEY_ENABLED);
    }

    protected function prepareContext(ContextInterface $source, WriteableContextInterface $target): void
    {
    }

    abstract protected function collect(IdentifierInterface $identifier): ?InboundRouteResultInterface;

    public function addContext(ContextInterface $source, WriteableContextInterface $target): void
    {
        if ($this->proceed()) {
            $this->prepareContext($source, $target);
        }
    }

    /**
     * @throws InvalidIdentifierException
     */
    public function getData(IdentifierInterface $identifier): ?InboundRouteResultInterface
    {
        $result = null;
        if ($this->proceed()) {
            $result = $this->collect($identifier);
            if ($result instanceof InboundRouteResultInterface) {
                $dataMapperGroupId = $this->getConfig(static::KEY_DATA_MAP);
                $dataMapperGroupConfig = $this->collectorConfiguration->getDataMapperGroupConfiguration($dataMapperGroupId);
                $context = $this->dataProcessor->createContext($result->getData(), $this->collectorConfiguration);
                $data = $this->dataProcessor->processDataMapperGroup(
                    $dataMapperGroupConfig,
                    $context
                );
                $result->setData($data);
            }
        }

        return $result;
    }

    public function getCacheTimeoutInSeconds(): ?int
    {
        return InheritableIntegerSchema::convert($this->getConfig(static::KEY_CACHE_TIMEOUT_IN_SECONDS));
    }

    public static function getDefaultFields(): array
    {
        return [];
    }

    public function getConfiguredWeight(): int
    {
        // NOTE The priority is sorted descending, while the weight is sorted ascending,
        //      so, a higher priority means a lower weight.
        return 20 - $this->getConfig(static::KEY_PRIORITY);
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setIcon('inbound-route');

        $label = static::getLabel();
        if ($label !== null) {
            $schema->getRenderingDefinition()->setLabel($label);
        }

        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));

        $prioritySchema = new IntegerSchema(static::DEFAULT_PRIORITY);
        $prioritySchema->getRenderingDefinition()->setHint('Routes with a higher priority will take precedence and can add their fields first. Once a field is written, it will not be overwritten by other routes.');
        $schema->addProperty(static::KEY_PRIORITY, $prioritySchema);

        $cacheLifetimeSchema = new InheritableIntegerSchema();
        $cacheLifetimeSchema->addReference(
            sprintf(
                '/%s/%s/%s/%s',
                ConfigurationInterface::KEY_INTEGRATIONS,
                ConfigurationInterface::KEY_GENERAL_INTEGRATION,
                CollectorConfigurationInterface::KEY_INBOUND_ROUTES,
                CollectorConfigurationInterface::KEY_CACHE_TIMEOUT
            ),
            label: 'Original Value ({.})'
        );
        $schema->addProperty(static::KEY_CACHE_TIMEOUT_IN_SECONDS, $cacheLifetimeSchema);

        $schema->addProperty(static::KEY_DATA_MAP, new CustomSchema(DataMapperGroupReferenceSchema::TYPE));

        return $schema;
    }
}
