<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;

interface CollectorRegistryInterface
{
    public function getCollector(): CollectorInterface;

    public function setCollector(CollectorInterface $collector): void;
}
