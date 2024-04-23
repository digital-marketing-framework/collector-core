<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface CollectorInterface
{
    /**
     * @param array<string> $fieldGroups
     */
    public function collect(
        CollectorConfigurationInterface $configuration,
        array $fieldGroups = [],
        ?WriteableContextInterface $preparedContext = null,
        bool $invalidIdentifierHandling = false
    ): DataInterface;

    /**
     * @param array<string> $fieldGroups
     */
    public function prepareContext(
        CollectorConfigurationInterface $configuration,
        array $fieldGroups = [],
        ?WriteableContextInterface $context = null
    ): WriteableContextInterface;
}
