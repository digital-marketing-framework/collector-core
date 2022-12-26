<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataCollectorRegistryTrait;
use DigitalMarketingFramework\Collector\Core\Service\Collector;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryTrait;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryTrait;

trait RegistryTrait
{
    use ConfigurationResolverRegistryTrait;
    use DataCollectorRegistryTrait;
    use IdentifierCollectorRegistryTrait;

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

    public function getDefaultCollectorConfiguration(): array
    {
        return [
            CollectorConfigurationInterface::KEY_DATA_COLLECTORS => $this->getDataCollectorDefaultConfigurations(),
        ];
    }

    public function getDefaultConfiguration(): array
    {
        return $this->getDefaultCollectorConfiguration();
    }
}
