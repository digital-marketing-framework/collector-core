<?php

namespace DigitalMarketingFramework\Collector\Core\DataCollector;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Result\DataCollectorResultInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Collector\Core\Plugin\Plugin;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;

abstract class DataCollector extends Plugin implements DataCollectorInterface, DataProcessorAwareInterface
{
    use ConfigurationTrait;
    use DataProcessorAwareTrait;

    protected const KEY_ENABLED = 'enabled';
    protected const DEFAULT_ENABLED = false;

    protected const KEY_DATA_MAP = 'dataMap';

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected CollectorConfigurationInterface $collectorConfiguration,
    ) {
        parent::__construct($keyword, $registry);
        $this->configuration = $collectorConfiguration->getDataCollectorConfiguration($this->getKeyword());
    }

    protected function proceed(): bool
    {
        return (bool)$this->getConfig(static::KEY_ENABLED);
    }

    protected function prepareContext(ContextInterface $source, WriteableContextInterface $target): void
    {
    }

    abstract protected function collect(IdentifierInterface $identifier): ?DataCollectorResultInterface;

    public function addContext(ContextInterface $source, WriteableContextInterface $target): void
    {
        if ($this->proceed()) {
            $this->prepareContext($source, $target);
        }
    }

    /**
     * @throws InvalidIdentifierException
     */
    public function getData(IdentifierInterface $identifier): ?DataCollectorResultInterface
    {
        $result = null;
        if ($this->proceed()) {
            $result = $this->collect($identifier);
            if ($result !== null) {
                $data = $this->dataProcessor->processDataMapper(
                    $this->getConfig(static::KEY_DATA_MAP), 
                    $result->getData(), 
                    $this->collectorConfiguration
                );
                $result->setData($data);
            }
        }
        return $result;
    }

    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_ENABLED => static::DEFAULT_ENABLED,
            static::KEY_DATA_MAP => DataProcessor::getDefaultDataMapperConfiguration(),
        ];
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));
        $schema->addProperty(static::KEY_DATA_MAP, new CustomSchema(DataMapperSchema::TYPE));
        return $schema;
    }
}
