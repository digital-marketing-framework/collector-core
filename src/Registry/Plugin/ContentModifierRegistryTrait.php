<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;

trait ContentModifierRegistryTrait
{
    use PluginRegistryTrait;

    public function registerContentModifier(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(ContentModifierInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteContentModifier(string $keyword): void
    {
        $this->deletePlugin($keyword, ContentModifierInterface::class);
    }

    public function getContentModifier(string $keyword, ConfigurationInterface $configuration): ?ContentModifierInterface
    {
        /** @var ?ContentModifierInterface */
        return $this->getPlugin($keyword, ContentModifierInterface::class, [CollectorConfiguration::convert($configuration)]);
    }

    /**
     * @return array<ContentModifierInterface>
     */
    public function getAllContentModifiers(ConfigurationInterface $configuration): array
    {
        /** @var array<ContentModifierInterface> */
        return $this->getAllPlugins(ContentModifierInterface::class, [$configuration]);
    }

    public function getContentModifierSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        foreach ($this->getAllPluginClasses(ContentModifierInterface::class) as $key => $class) {
            $schema->addProperty($key, $class::getSchema());
        }

        return $schema;
    }
}
