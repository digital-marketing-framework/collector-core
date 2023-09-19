<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

interface CollectorAwareInterface
{
    public function setCollector(CollectorInterface $collector): void;
}
