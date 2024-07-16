<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Api\RouteResolver\CollectorRouteResolverInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface CollectorApiRegistryInterface
{
    public function getCollectorApiRouteResolver(): CollectorRouteResolverInterface;

    /**
     * @return array<string,RouteResolverInterface>
     */
    public function getApiRouteResolvers(): array;

    public function getCollectorRequestHandler(): CollectorRequestHandlerInterface;

    public function setCollectorRequestHandler(CollectorRequestHandlerInterface $collectorRequestHandler): void;
}
