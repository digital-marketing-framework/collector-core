<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\Registry\Plugin\PluginRegistryInterface;

interface CollectorRegistryInterface extends PluginRegistryInterface
{
    public function registerDataCollector(string $class, array $additionalArguments = [], string $keyword = ''): void;
    public function deleteDataCollector(string $keyword): void;
    public function getDataCollector(string $keyword): ?DataCollectorInterface;

    /**
     * @return array<DataCollectorInterface>
     */
    public function getAllDataCollectors(): array;
}
