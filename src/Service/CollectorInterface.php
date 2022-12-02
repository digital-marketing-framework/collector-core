<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface CollectorInterface
{
    public function merge(array ...$dataSets): DataInterface;
    public function collect(ContextInterface $context, CollectorConfigurationInterface $configuration): DataInterface;
}
