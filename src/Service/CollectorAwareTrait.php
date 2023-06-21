<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

trait CollectorAwareTrait
{
    protected Collector $collector;

    public function setCollector(Collector $collector): void
    {
        $this->collector = $collector;
    }
}
