<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\Registry\Plugin\ContentModifierRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataTransformationRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\InboundRouteRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Service\CollectorRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Service\InvalidIdentifierHandlerRegistryInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface as CoreRegistryInterface;

interface RegistryInterface extends
    CoreRegistryInterface,
    InvalidIdentifierHandlerRegistryInterface,
    InboundRouteRegistryInterface,
    CollectorRegistryInterface,
    DataTransformationRegistryInterface,
    ContentModifierRegistryInterface
{
}
