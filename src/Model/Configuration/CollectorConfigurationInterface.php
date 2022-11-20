<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface CollectorConfigurationInterface extends ConfigurationInterface
{
    public const KEY_COLLECTOR = 'collector';
    public const KEY_DATA_COLLECTORS = 'collectors';

    public function getCollectorConfiguration(): array;

    public function dataCollectorExists(string $dataCollectorName): bool;
    public function getDataCollectorConfiguration(string $dataCollectorName): array;
}
