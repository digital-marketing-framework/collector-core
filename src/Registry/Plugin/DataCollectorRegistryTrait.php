<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DataCollector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;

trait DataCollectorRegistryTrait
{
    use PluginRegistryTrait;

    public function registerDataCollector(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(DataCollectorInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteDataCollector(string $keyword): void
    {
        $this->deletePlugin($keyword, DataCollectorInterface::class);
    }
    
    public function getDataCollector(string $keyword, CollectorConfigurationInterface $configuration): ?DataCollectorInterface
    {
        return $this->getPlugin($keyword, DataCollectorInterface::class, [$configuration]);
    }

    /**
     * @return array<DataCollectorInterface>
     */
    public function getAllDataCollectors(CollectorConfigurationInterface $configuration): array
    {
        return $this->getAllPlugins(DataCollectorInterface::class, [$configuration]);
    }
}
