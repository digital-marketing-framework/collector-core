<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandler;
use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Api\RouteResolver\CollectorRouteResolver;
use DigitalMarketingFramework\Collector\Core\Api\RouteResolver\CollectorRouteResolverInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

trait CollectorApiRegistryTrait
{
    protected CollectorRequestHandlerInterface $collectorRequestHandler;

    protected CollectorRouteResolverInterface $collectorRouteResolver;

    abstract public function getContentModifierHandler(): ContentModifierHandlerInterface;

    public function getCollectorApiRouteResolver(): CollectorRouteResolverInterface
    {
        if (!isset($this->collectorRouteResolver)) {
            $this->collectorRouteResolver = $this->createObject(CollectorRouteResolver::class, [$this]);
        }

        return $this->collectorRouteResolver;
    }

    public function getApiRouteResolvers(): array
    {
        return [
            'collector' => $this->getCollectorApiRouteResolver(),
        ];
    }

    public function getCollectorRequestHandler(): CollectorRequestHandlerInterface
    {
        if (!isset($this->collectorRequestHandler)) {
            $this->collectorRequestHandler = $this->createObject(CollectorRequestHandler::class, [$this]);
        }

        return $this->collectorRequestHandler;
    }

    public function setCollectorRequestHandler(CollectorRequestHandlerInterface $collectorRequestHandler): void
    {
        $this->collectorRequestHandler = $collectorRequestHandler;
    }

    /**
     * @param array<string,mixed> $settings
     */
    protected function addContentModifierFrontendSettings(array &$settings, EndPointInterface $endPoint, CollectorConfigurationInterface $configuration): void
    {
        $contentModifierRoute = $this->getCollectorApiRouteResolver()->getContentModifierRoute();
        $contentModifiers = $this->getFrontendContentModifiers($configuration);
        $entryRouteResolver = $this->getRegistryCollection()->getApiEntryRouteResolver();
        $endPointName = $endPoint->getName();

        foreach ($contentModifiers as $contentModifier) {
            $keyword = $contentModifier->getKeyword();
            $name = $contentModifier->getContentModifierName();
            $contentModifierSettings = $contentModifier->getFrontendSettings();

            $route = $contentModifierRoute->getResourceRoute(
                idAffix: implode(':', [$keyword, $endPointName, $name]),
                variables: [
                    RouteResolverInterface::VARIABLE_END_POINT => GeneralUtility::slugify($endPointName),
                    CollectorRouteResolverInterface::VARIABLE_PLUGIN_TYPE => GeneralUtility::slugify($keyword),
                    CollectorRouteResolverInterface::VARIABLE_PLUGIN_ID => GeneralUtility::slugify($name),
                ]
            );

            $id = $route->getId();
            $settings['pluginSettings'][$id] = $contentModifierSettings;
            $settings['urls'][$id] = $entryRouteResolver->getFullPath($route->getPath());
        }
    }

    /**
     * @param array<string,mixed> $settings
     */
    protected function addDataTransformationFrontendSettings(array &$settings, EndPointInterface $endPoint, CollectorConfigurationInterface $configuration): void
    {
        $transformationRoute = $this->getCollectorApiRouteResolver()->getUserDataRoute();
        $transformationNames = $this->getPublicDataTransformationNames($configuration);
        $entryRouteResolver = $this->getRegistryCollection()->getApiEntryRouteResolver();
        $endPointName = $endPoint->getName();

        foreach ($transformationNames as $transformationName) {
            $route = $transformationRoute->getResourceRoute(
                idAffix: implode(':', [$endPointName, $transformationName]),
                variables: [
                    RouteResolverInterface::VARIABLE_END_POINT => GeneralUtility::slugify($endPointName),
                    CollectorRouteResolverInterface::VARIABLE_TRANSFORMATION_ID => GeneralUtility::slugify($transformationName),
                ]
            );

            $id = $route->getId();
            $settings['pluginSettings'][$id] = [];
            $settings['urls'][$id] = $entryRouteResolver->getFullPath($route->getPath());
        }
    }

    /**
     * @param array{settings:array<string,mixed>,urls:array<string,string>,pluginSettings:array<string,array<string,mixed>>} $settings
     */
    protected function addContentSpecificSettings(array &$settings): void
    {
        $contentSettings = $this->getContentModifierHandler()->getContentSpecificSettings();
        $settings['content'] = $contentSettings === [] ? (object)$contentSettings : $contentSettings;
    }

    public function getFrontendSettings(): array
    {
        $settings = parent::getFrontendSettings();
        $configurationDocumentManager = $this->getConfigurationDocumentManager();
        $endPointStorage = $this->getEndPointStorage();
        foreach ($endPointStorage->getAllEndPoints() as $endPoint) {
            if (!$endPoint->getEnabled() || !$endPoint->getPullEnabled() || !$endPoint->getExposeToFrontend()) {
                continue;
            }

            $configuration = new CollectorConfiguration($configurationDocumentManager->getConfigurationStackFromDocument($endPoint->getConfigurationDocument()));
            $this->addContentModifierFrontendSettings($settings, $endPoint, $configuration);
            $this->addDataTransformationFrontendSettings($settings, $endPoint, $configuration);
        }

        $this->addContentSpecificSettings($settings);

        return $settings;
    }
}
