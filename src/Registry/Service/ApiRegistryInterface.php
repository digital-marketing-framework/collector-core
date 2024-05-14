<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Api\RouteResolver\CollectorRouteResolverInterface;

interface ApiRegistryInterface
{
    public function getCollectorApiRouteResolver(): CollectorRouteResolverInterface;

    public function getApiRouteResolvers(): array;

    public function getCollectorRequestHandler(): CollectorRequestHandlerInterface;

    public function setCollectorRequestHandler(CollectorRequestHandlerInterface $collectorRequestHandler): void;
}
