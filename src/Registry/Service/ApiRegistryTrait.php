<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandler;
use DigitalMarketingFramework\Collector\Core\Api\CollectorRequestHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Api\RouteResolver\CollectorRouteResolver;
use DigitalMarketingFramework\Collector\Core\Api\RouteResolver\CollectorRouteResolverInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

trait ApiRegistryTrait
{
    protected CollectorRequestHandlerInterface $collectorRequestHandler;

    protected CollectorRouteResolverInterface $collectorRouteResolver;

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

    protected function addContentModifierFrontendSettings(array &$settings, CollectorConfigurationInterface $configuration): void
    {
        $contentModifierRoute = $this->getCollectorApiRouteResolver()->getContentModifierRoute();
        $contentModifiers = $this->getFrontendContentModifiers($configuration);
        $entryRouteResolver = $this->getApiEntryRouteResolver();

        foreach ($contentModifiers as $contentModifier) {
            $keyword = $contentModifier->getKeyword();
            $name = $contentModifier->getContentModifierName();
            $contentModifierSettings = $contentModifier->getFrontendSettings();

            $route = $contentModifierRoute->getResourceRoute(
                idAffix: implode(':', [$keyword, $name]),
                variables: [
                    CollectorRouteResolverInterface::VARIABLE_PLUGIN_TYPE => GeneralUtility::slugify($keyword),
                    CollectorRouteResolverInterface::VARIABLE_PLUGIN_ID => GeneralUtility::slugify($name),
                ]
            );

            $id = $route->getId();
            $settings['pluginSettings'][$id] = $contentModifierSettings;
            $settings['urls'][$id] = $entryRouteResolver->getFullPath($route->getPath());
        }
    }

    protected function addDataTransformationFrontendSettings(array &$settings, CollectorConfigurationInterface $configuration): void
    {
        $transformationRoute = $this->getCollectorApiRouteResolver()->getUserDataRoute();
        $transformationNames = $this->getPublicDataTransformationNames($configuration);
        $entryRouteResolver = $this->getApiEntryRouteResolver();

        foreach ($transformationNames as $transformationName) {
            $route = $transformationRoute->getResourceRoute(
                idAffix: $transformationName,
                variables: [
                    CollectorRouteResolverInterface::VARIABLE_TRANSFORMATION_ID => GeneralUtility::slugify($transformationName),
                ]
            );

            $id = $route->getId();
            $settings['pluginSettings'][$id] = [];
            $settings['urls'][$id] = $entryRouteResolver->getFullPath($route->getPath());
        }
    }

    public function getFrontendSettings(): array
    {
        $settings = parent::getFrontendSettings();
        $configurationDocumentManager = $this->getConfigurationDocumentManager();
        $configuration = new CollectorConfiguration($configurationDocumentManager->getDefaultConfigurationStack());
        $this->addContentModifierFrontendSettings($settings, $configuration);
        $this->addDataTransformationFrontendSettings($settings, $configuration);
        return $settings;
    }
}
