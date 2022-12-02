<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\Configuration;

class CollectorConfiguration extends Configuration implements CollectorConfigurationInterface
{
    public function getDataCollectorConfiguration(string $dataCollectorName): array
    {
        return $this->get(static::KEY_DATA_COLLECTORS, [])[$dataCollectorName] ?? [];
    }

    public function dataCollectorExists(string $dataCollectorName): bool
    {
        return isset($this->get(static::KEY_DATA_COLLECTORS, [])[$dataCollectorName]);
    }
}
