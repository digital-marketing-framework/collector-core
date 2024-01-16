<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

trait CollectorAwareTrait
{
    protected CollectorInterface $collector;

    public function setCollector(CollectorInterface $collector): void
    {
        $this->collector = $collector;
    }
}
