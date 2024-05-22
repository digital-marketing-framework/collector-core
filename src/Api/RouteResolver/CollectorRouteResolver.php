<?php

namespace DigitalMarketingFramework\Collector\Core\Api\RouteResolver;

use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Api\ApiException;
use DigitalMarketingFramework\Core\Api\Request\ApiRequestInterface;
use DigitalMarketingFramework\Core\Api\Response\ApiResponse;
use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\Route\TemplateRoute;
use DigitalMarketingFramework\Core\Api\Route\TemplateRouteInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class CollectorRouteResolver implements CollectorRouteResolverInterface
{
    protected CollectorRequestHandlerInterface $collectorRequestHandler;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
        $this->collectorRequestHandler = $this->registry->getCollectorRequestHandler();
    }

    protected function resolveContentModifierRequest(ApiRequestInterface $request): ApiResponseInterface
    {
        $endPoint = $request->getEndPoint();
        $plugin = GeneralUtility::dashedToCamelCase($request->getVariable(static::VARIABLE_PLUGIN_TYPE));
        $name = GeneralUtility::dashedToCamelCase($request->getVariable(static::VARIABLE_PLUGIN_ID));

        $data = $this->collectorRequestHandler->processContentModifier($endPoint, $plugin, $name);

        return new ApiResponse($data);
    }

    protected function resolveUserDataRequest(ApiRequestInterface $request): ApiResponseInterface
    {
        $endPoint = $request->getEndPoint();
        $name = GeneralUtility::dashedToCamelCase($request->getVariable(static::VARIABLE_TRANSFORMATION_ID));
        $data = $this->collectorRequestHandler->processUserData($endPoint, $name);

        return new ApiResponse($data);
    }

    public function resolveRequest(ApiRequestInterface $request): ApiResponseInterface
    {
        $module = GeneralUtility::dashedToCamelCase($request->getVariable(self::VARIABLE_MODULE));
        return match ($module) {
            static::SEGMENT_CONTENT_MODIFIER => $this->resolveContentModifierRequest($request),
            static::SEGMENT_USER_DATA => $this->resolveUserDataRequest($request),
            default => throw new ApiException(sprintf('Collector module "%s" unknown.', $module)),
        };

    }

    public function getContentModifierRoute(): TemplateRouteInterface
    {
        return new TemplateRoute(
            id: implode(':', [
                static::SEGMENT_COLLECTOR,
                static::SEGMENT_CONTENT_MODIFIER,
            ]),
            template: implode('/', [
                GeneralUtility::slugify(static::SEGMENT_COLLECTOR),
                '{' . static::VARIABLE_END_POINT . '}',
                GeneralUtility::slugify(static::SEGMENT_CONTENT_MODIFIER),
                '{' . static::VARIABLE_PLUGIN_TYPE .'}',
                '{' . static::VARIABLE_PLUGIN_ID .'}',
            ]),
            variables: [
                static::VARIABLE_END_POINT => '',
                static::VARIABLE_PLUGIN_TYPE => '',
                static::VARIABLE_PLUGIN_ID => '',
            ],
            constants: [
                static::VARIABLE_DOMAIN => static::SEGMENT_COLLECTOR,
                static::VARIABLE_MODULE => static::SEGMENT_CONTENT_MODIFIER,
            ],
            methods: ['GET']
        );
    }

    public function getUserDataRoute(): TemplateRouteInterface
    {
        return new TemplateRoute(
            id: implode(':', [
                static::SEGMENT_COLLECTOR,
                static::SEGMENT_USER_DATA,
            ]),
            template: implode('/', [
                GeneralUtility::slugify(static::SEGMENT_COLLECTOR),
                '{' . static::VARIABLE_END_POINT . '}',
                GeneralUtility::slugify(static::SEGMENT_USER_DATA),
                '{' . static::VARIABLE_TRANSFORMATION_ID .'}',
            ]),
            variables: [
                static::VARIABLE_END_POINT => '',
                static::VARIABLE_TRANSFORMATION_ID => '',
            ],
            constants: [
                static::VARIABLE_DOMAIN => static::SEGMENT_COLLECTOR,
                static::VARIABLE_MODULE => static::SEGMENT_USER_DATA,
            ],
            methods: ['GET']
        );
    }

    public function getAllRoutes(): array
    {
        return [
            $this->getContentModifierRoute(),
            $this->getUserDataRoute(),
        ];
    }

    public function getAllResourceRoutes(): array
    {
        $routes = [];

        $contentModifierRoute = $this->getContentModifierRoute();
        foreach ($this->collectorRequestHandler->getContentModifierPlugins() as $endPointName => $plugins) {
            foreach ($plugins as $plugin => $names) {
                foreach ($names as $name) {
                    $route = $contentModifierRoute->getResourceRoute(
                        idAffix: implode(':', [$plugin, $name]),
                        variables: [
                            static::VARIABLE_END_POINT => GeneralUtility::slugify($endPointName),
                            static::VARIABLE_PLUGIN_TYPE => GeneralUtility::slugify($plugin),
                            static::VARIABLE_PLUGIN_ID => GeneralUtility::slugify($name),
                        ]
                    );
                    $routes[$route->getId()] = $route;
                }
            }
        }

        $userDataRoute = $this->getUserDataRoute();
        foreach ($this->collectorRequestHandler->getUserDataSets() as $endPointName => $sets) {
            foreach ($sets as $name) {
                $route = $userDataRoute->getResourceRoute(
                    idAffix: $name,
                    variables: [
                        static::VARIABLE_END_POINT => GeneralUtility::slugify($endPointName),
                        static::VARIABLE_TRANSFORMATION_ID => GeneralUtility::slugify($name),
                    ]
                );
                $routes[$route->getId()] = $route;
            }
        }

        return $routes;
    }
}
