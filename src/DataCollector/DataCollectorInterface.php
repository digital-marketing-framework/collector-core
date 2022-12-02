<?php

namespace DigitalMarketingFramework\Collector\Core\DataCollector;

use DigitalMarketingFramework\Collector\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface DataCollectorInterface extends PluginInterface
{
    /**
     * @throws InvalidIdentifierException
     */
    public function getIdentifier(ContextInterface $context): ?IdentifierInterface;
    
    /**
     * @throws InvalidIdentifierException
     */
    public function getData(IdentifierInterface $identifier): ?DataInterface;

    public static function getDefaultConfiguration(): array;
}
