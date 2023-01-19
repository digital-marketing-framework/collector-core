<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface CollectorInterface
{
    public const KEY_DEFAULT_MAP = 'defaultMap';
    public const DEFAULT_DEFAULT_MAP = null;

    public function collect(
        CollectorConfigurationInterface $configuration,
        array|string|null $dataMap = null,
        ?WriteableContextInterface $preparedContext = null,
        bool $invalidIdentifierHandling = false
    ): DataInterface;

    public function prepareContext(
        CollectorConfigurationInterface $configuration,
        ?WriteableContextInterface $context = null
    ): WriteableContextInterface;

    public static function getDefaultConfiguration(): array;
}
