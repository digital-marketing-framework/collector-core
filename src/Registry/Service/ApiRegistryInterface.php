<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Api\RouteResolver\CollectorRouteResolverInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;

interface ApiRegistryInterface
{
    public function getCollectorApiRouteResolver(): CollectorRouteResolverInterface;

    /**
     * @return array<string,RouteResolverInterface>
     */
    public function getApiRouteResolvers(): array;

    public function getCollectorRequestHandler(): CollectorRequestHandlerInterface;

    public function setCollectorRequestHandler(CollectorRequestHandlerInterface $collectorRequestHandler): void;
}
