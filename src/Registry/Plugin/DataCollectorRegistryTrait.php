<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
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

    public function getDataCollector(string $keyword, ConfigurationInterface $configuration): ?DataCollectorInterface
    {
        return $this->getPlugin($keyword, DataCollectorInterface::class, [CollectorConfiguration::convert($configuration)]);
    }

    /**
     * @return array<DataCollectorInterface>
     */
    public function getAllDataCollectors(ConfigurationInterface $configuration): array
    {
        return $this->getAllPlugins(DataCollectorInterface::class, [$configuration]);
    }
}
