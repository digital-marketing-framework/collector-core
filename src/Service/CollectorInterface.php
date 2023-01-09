<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface CollectorInterface
{
    public function collect(CollectorConfigurationInterface $configuration, array|string|null $dataMap = null): DataInterface;
    public function prepareContext(ContextInterface $context, CollectorConfigurationInterface $configuration): WriteableContextInterface;

    public static function getDefaultConfiguration(): array;
}
