<?php

namespace DigitalMarketingFramework\Collector\Core\DataCollector;

use DigitalMarketingFramework\Collector\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Collector\Core\Model\Indentifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;
use DigitalMarketingFramework\Core\Request\RequestInterface;

interface DataCollectorInterface extends PluginInterface
{
    public function getIdentifier(RequestInterface $request): ?IdentifierInterface;
    
    /**
     * @throws InvalidIdentifierException
     */
    public function getData(RequestInterface $request): ?DataInterface;

}
