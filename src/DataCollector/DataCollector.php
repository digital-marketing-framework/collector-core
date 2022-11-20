<?php

namespace DigitalMarketingFramework\Collector\Core\DataCollector;

use BadMethodCallException;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Result\DataCollectorResultInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Collector\Core\Plugin\Plugin;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\Helper\ConfigurationResolverTrait;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Service\DataProcessor;
use DigitalMarketingFramework\Core\Service\DataProcessorInterface;

abstract class DataCollector extends Plugin implements DataCollectorInterface
{
    use ConfigurationTrait;
    use ConfigurationResolverTrait;

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

    protected function mapData(DataInterface $data): DataInterface
    {
        $mapped = $this->resolveContent(
            ['dataMap' => $this->getConfig(static::KEY_DATA_MAP)],
            new ConfigurationResolverContext($data, ['configuration' => $this->collectorConfiguration])
        );
        if (!$mapped instanceof DataInterface) {
            $mapped = new Data();
        }
        return $mapped;
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
                $result->setData($this->mapData($result->getData()));
            }
        }
        return $result;
    }

    public static function getDefaultConfiguration(): array
    {
        $defaultConfig = [
            static::KEY_ENABLED => static::DEFAULT_ENABLED,
            static::KEY_DATA_MAP => DataProcessor::getDefaultConfiguration(),
        ];
        $defaultConfig[static::KEY_DATA_MAP][DataProcessorInterface::KEY_PASSTHROUGH_FIELDS] = true;
        return $defaultConfig;
    }
}
