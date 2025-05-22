<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

/** @phpstan-ignore-next-line This trait can be used by other packages, even though it is not used in this one. */
trait CollectorAwareTrait
{
    protected CollectorInterface $collector;

    public function setCollector(CollectorInterface $collector): void
    {
        $this->collector = $collector;
    }
}
