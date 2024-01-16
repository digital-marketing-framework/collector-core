<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataCollector\DataCollectorSchema;
use DigitalMarketingFramework\Collector\Core\DataCollector\DataCollectorInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
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
        /** @var ?DataCollectorInterface */
        return $this->getPlugin($keyword, DataCollectorInterface::class, [CollectorConfiguration::convert($configuration)]);
    }

    /**
     * @return array<DataCollectorInterface>
     */
    public function getAllDataCollectors(ConfigurationInterface $configuration): array
    {
        /** @var array<DataCollectorInterface> */
        return $this->getAllPlugins(DataCollectorInterface::class, [$configuration]);
    }

    public function getDataCollectorSchema(): SchemaInterface
    {
        $schema = new DataCollectorSchema();
        $schema->getRenderingDefinition()->setLabel('Data Collectors');

        foreach ($this->getAllPluginClasses(DataCollectorInterface::class) as $key => $class) {
            $schema->addItem($key, $class::getSchema());
        }

        return $schema;
    }
}
