<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

interface CollectorAwareInterface
{
    public function setCollector(Collector $collector): void;
}
