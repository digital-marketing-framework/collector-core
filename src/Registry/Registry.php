<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataCollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Registry\Service\InvalidIdentifierHandlerRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Service\Collector;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;

class Registry extends CoreRegistry implements RegistryInterface
{
    use InvalidIdentifierHandlerRegistryTrait;
    use DataCollectorRegistryTrait;

    protected CollectorInterface $collector;

    public function getCollector(): CollectorInterface
    {
        if (!isset($this->collector)) {
             $this->collector = $this->createObject(Collector::class, [$this]);
        }
        return $this->collector;
    }

    public function getDataCollectorDefaultConfigurations(): array
    {
        $result = [];
        foreach ($this->pluginClasses[DataCollectorInterface::class] ?? [] as $key => $class) {
            $result[$key] = $class::getDefaultConfiguration();
        }
        return $result;
    }

    public function getCollectorDefaultConfiguration(): array
    {
        $defaultCollectorConfiguration = Collector::getDefaultConfiguration();
        $defaultCollectorConfiguration[CollectorConfigurationInterface::KEY_DATA_COLLECTORS] = $this->getDataCollectorDefaultConfigurations();
        return $defaultCollectorConfiguration;
    }

    public function getDefaultConfiguration(): array
    {
        $defaultConfiguration = parent::getDefaultConfiguration();
        $defaultConfiguration[CollectorConfigurationInterface::KEY_COLLECTOR] = $this->getCollectorDefaultConfiguration();
        return $defaultConfiguration;
    }

    public function getConfigurationSchema(): array
    {
        return [];
    }
}
