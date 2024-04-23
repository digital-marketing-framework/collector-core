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
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Route\Route;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataMapperGroupReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\InheritableIntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class InboundRoute extends ConfigurablePlugin implements InboundRouteInterface, DataProcessorAwareInterface
{
    use DataProcessorAwareTrait;

    protected const KEY_ENABLED = 'enabled';

    protected const DEFAULT_ENABLED = false;

    public const KEY_CACHE_TIMEOUT_IN_SECONDS = 'cacheLifetime';

    protected const KEY_DATA_MAP = 'dataMap';

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected CollectorConfigurationInterface $collectorConfiguration,
    ) {
        parent::__construct($keyword, $registry);
        $this->configuration = $collectorConfiguration->getInboundRouteConfiguration(static::getIntegrationName(), $this->getKeyword());
    }

    abstract public static function getIntegrationName(): string;

    public static function getIntegrationLabel(): ?string
    {
        return null;
    }

    public static function getInboundRouteListLabel(): ?string
    {
        return null;
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

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();

        $label = static::getLabel();
        if ($label !== null) {
            $schema->getRenderingDefinition()->setLabel($label);
        }

        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));

        $cacheLifetimeSchema = new InheritableIntegerSchema();
        $cacheLifetimeSchema->getRenderingDefinition()->setLabel('Cache lifetime (seconds)');
        $schema->addProperty(static::KEY_CACHE_TIMEOUT_IN_SECONDS, $cacheLifetimeSchema);

        $schema->addProperty(static::KEY_DATA_MAP, new CustomSchema(DataMapperGroupReferenceSchema::TYPE));

        return $schema;
    }
}
