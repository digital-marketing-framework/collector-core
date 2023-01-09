<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\Configuration;

class CollectorConfiguration extends Configuration implements CollectorConfigurationInterface
{
    public function getCollectorConfiguration(bool $resolveNull = true): array
    {
        return $this->getMergedConfiguration($resolveNull)[static::KEY_COLLECTOR] ?? [];
    }

    public function getDataCollectorConfiguration(string $dataCollectorName): array
    {
        return $this->getCollectorConfiguration()[static::KEY_DATA_COLLECTORS][$dataCollectorName] ?? [];
    }

    public function dataCollectorExists(string $dataCollectorName): bool
    {
        return isset($this->getCollectorConfiguration()[static::KEY_DATA_COLLECTORS][$dataCollectorName]);
    }
}
