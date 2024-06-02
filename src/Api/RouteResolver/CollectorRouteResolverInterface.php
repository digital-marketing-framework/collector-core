<?php

namespace DigitalMarketingFramework\Collector\Core\Api\RouteResolver;

use DigitalMarketingFramework\Core\Api\Route\TemplateRouteInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;

interface CollectorRouteResolverInterface extends RouteResolverInterface
{
    public const SEGMENT_COLLECTOR = 'collector';

    public const SEGMENT_CONTENT_MODIFIER = 'contentModifiers';

    public const SEGMENT_USER_DATA = 'userData';

    public const VARIABLE_MODULE = 'module';

    public const VARIABLE_TRANSFORMATION_ID = 'transformation_id';

    public const VARIABLE_PLUGIN_TYPE = 'plugin_type';

    public const VARIABLE_PLUGIN_ID = 'plugin_id';

    public function getContentModifierRoute(): TemplateRouteInterface;

    public function getUserDataRoute(): TemplateRouteInterface;
}
