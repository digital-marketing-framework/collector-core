<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryInterface;

interface DataCollectorRegistryInterface extends PluginRegistryInterface
{
    public function registerDataCollector(string $class, array $additionalArguments = [], string $keyword = ''): void;
    public function deleteDataCollector(string $keyword): void;
    public function getDataCollector(string $keyword, CollectorConfigurationInterface $configuration): ?DataCollectorInterface;

    /**
     * @return array<DataCollectorInterface>
     */
    public function getAllDataCollectors(CollectorConfigurationInterface $configuration): array;

    /**
     * @return array<mixed>
     */
    public function getDataCollectorDefaultConfigurations(): array;
}
