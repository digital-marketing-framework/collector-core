<?php

namespace DigitalMarketingFramework\Collector\Core\DataCollector;

use BadMethodCallException;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Collector\Core\Plugin\Plugin;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationResolverTrait;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Service\DataProcessor;

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

    abstract protected function collectIdentifier(ContextInterface $context): ?IdentifierInterface;
    abstract protected function collect(IdentifierInterface $identifier): ?DataInterface;

    protected function getConfigurationResolverContext(): ConfigurationResolverContextInterface
    {
        throw new BadMethodCallException('Data collectors should have provided a resolver context already!');
    }

    /**
     * @throws InvalidIdentifierException
     */
    public function getIdentifier(ContextInterface $context): ?IdentifierInterface
    {
        if ($this->proceed()) {
            return $this->collectIdentifier($context);
        }
        return null;
    }

    protected function mapData(DataInterface $data): DataInterface
    {
        $mapped = $this->resolveContent(
            ['dataMap' => $this->configuration], 
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
    public function getData(IdentifierInterface $identifier): ?DataInterface
    {
        $data = null;
        if ($this->proceed()) {
            $data = $this->collect($identifier);
            if ($data !== null) {
                $data = $this->mapData($data);
            }
        }
        return $data;
    }

    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_ENABLED => static::DEFAULT_ENABLED,
            static::KEY_DATA_MAP => DataProcessor::getDefaultConfiguration(),
        ];
    }
}
