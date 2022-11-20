<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DataCollector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;

trait CollectorRegistryTrait
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
    
    public function getDataCollector(string $keyword): ?DataCollectorInterface
    {
        return $this->getConfigurationResolver($keyword);
    }

    /**
     * @return array<DataCollectorInterface>
     */
    public function getAllDataCollectors(): array
    {
        return $this->getAllPlugins(DataCollectorInterface::class);
    }
}
