<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Route\InboundRouteInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface CollectorInterface
{
    /**
     * @param array<string> $fieldGroups
     */
    public function collect(
        CollectorConfigurationInterface $configuration,
        array $fieldGroups = [InboundRouteInterface::STANDARD_FIELD_GROUP],
        bool $invalidIdentifierHandling = false,
    ): DataInterface;

    /**
     * @param array<string> $fieldGroups
     */
    public function prepareContext(
        CollectorConfigurationInterface $configuration,
        WriteableContextInterface $context,
        array $fieldGroups = [InboundRouteInterface::STANDARD_FIELD_GROUP],
    ): void;

    /**
     * @param array<string> $fieldGroups
     */
    public function prepareContextAndCollect(
        CollectorConfigurationInterface $configuration,
        WriteableContextInterface $context,
        array $fieldGroups = [InboundRouteInterface::STANDARD_FIELD_GROUP],
        bool $invalidIdentifierHandling = false,
    ): DataInterface;
}
