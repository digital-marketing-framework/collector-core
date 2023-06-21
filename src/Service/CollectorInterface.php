<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface CollectorInterface
{
    public function collect(
        CollectorConfigurationInterface $configuration,
        ?WriteableContextInterface $preparedContext = null,
        bool $invalidIdentifierHandling = false
    ): DataInterface;

    public function prepareContext(
        CollectorConfigurationInterface $configuration,
        ?WriteableContextInterface $context = null
    ): WriteableContextInterface;
}
