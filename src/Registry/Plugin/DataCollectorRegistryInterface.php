<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryInterface;

interface DataCollectorRegistryInterface extends PluginRegistryInterface
{
    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerDataCollector(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteDataCollector(string $keyword): void;

    public function getDataCollector(string $keyword, ConfigurationInterface $configuration): ?DataCollectorInterface;

    /**
     * @return array<DataCollectorInterface>
     */
    public function getAllDataCollectors(ConfigurationInterface $configuration): array;

    public function getDataCollectorSchema(): SchemaInterface;
}
