<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\Route\InboundRouteInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface InboundRouteRegistryInterface extends PluginRegistryInterface
{
    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerInboundRoute(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteInboundRoute(string $keyword): void;

    public function getInboundRoute(string $keyword, ConfigurationInterface $configuration): ?InboundRouteInterface;

    /**
     * @return array<InboundRouteInterface>
     */
    public function getAllInboundRoutes(ConfigurationInterface $configuration): array;
}
