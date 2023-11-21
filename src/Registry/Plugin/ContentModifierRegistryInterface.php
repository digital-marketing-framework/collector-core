<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierInterface;
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

    public function getContentModifier(string $keyword, ConfigurationInterface $configuration): ?ContentModifierInterface;

    /**
     * @return array<ContentModifierInterface>
     */
    public function getAllContentModifiers(ConfigurationInterface $configuration): array;

    public function getContentModifierSchema(): SchemaInterface;
}
