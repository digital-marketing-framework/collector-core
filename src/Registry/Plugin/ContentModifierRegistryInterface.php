<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendContentModifierInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryInterface;

interface ContentModifierRegistryInterface extends PluginRegistryInterface
{
    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerContentModifier(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteContentModifier(string $keyword): void;

    public function getContentModifier(ConfigurationInterface $configuration, string $contentModifierId): ?ContentModifierInterface;

    /**
     * @return array<string,ContentModifierInterface>
     */
    public function getContentModifiers(ConfigurationInterface $configuration): array;

    /**
     * @return array<string,FrontendContentModifierInterface>
     */
    public function getFrontendContentModifiers(ConfigurationInterface $configuration): array;

    public function getContentModifierSchema(): SchemaInterface;
}
