<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Service\Collector;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;

trait CollectorRegistryTrait
{
    protected CollectorInterface $collector;

    abstract protected function createObject(string $class, array $arguments = []): object;

    public function getCollector(): CollectorInterface
    {
        if (!isset($this->collector)) {
            $this->collector = $this->createObject(Collector::class, [$this]);
        }

        return $this->collector;
    }

    public function setCollector(CollectorInterface $collector): void
    {
        $this->collector = $collector;
    }
}
