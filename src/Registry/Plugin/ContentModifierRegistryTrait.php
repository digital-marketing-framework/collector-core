<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\ContentModifier\ContentModifierSchema;
use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
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

    public function getContentModifier(ConfigurationInterface $configuration, string $contentModifierId): ?ContentModifierInterface
    {
        $configuration = CollectorConfiguration::convert($configuration);
        $keyword = $configuration->getContentModifierKeyword($contentModifierId);
        $name = $configuration->getContentModifierName($contentModifierId);

        return $this->getPlugin($keyword, ContentModifierInterface::class, [$configuration, $contentModifierId, $name]);
    }

    /**
     * @return array<string,ContentModifierInterface>
     */
    public function getContentModifiers(ConfigurationInterface $configuration): array
    {
        $configuration = CollectorConfiguration::convert($configuration);
        $contentModifiers = [];
        foreach ($configuration->getContentModifierIds() as $contentModifierId) {
            $contentModifiers[$configuration->getContentModifierName($contentModifierId)] = $this->getContentModifier($configuration, $contentModifierId);
        }

        return $contentModifiers;
    }

    /**
     * @return array<string,FrontendContentModifierInterface>
     */
    public function getFrontendContentModifiers(ConfigurationInterface $configuration): array
    {
        $contentModifiers = $this->getContentModifiers($configuration);
        $contentModifiers = array_filter($contentModifiers, function(ContentModifierInterface $contentModifier) {
            return $contentModifier instanceof FrontendContentModifierInterface;
        });
        return $contentModifiers;
    }

    public function getContentModifierSchema(): SchemaInterface
    {
        $schema = new ContentModifierSchema();
        foreach ($this->getAllPluginClasses(ContentModifierInterface::class) as $key => $class) {
            $schema = $class::getSchema();
            $label = $class::getLabel();
            $schema->addItem($key, $schema, $label);
        }
        return $schema;
    }

    public function getContentModifiersSchema(SchemaDocument $schemaDocument): SchemaInterface
    {
        $contentModifierSchema = $this->getContentModifierSchema();
        $schemaDocument->addCustomType($contentModifierSchema, ContentModifierSchema::TYPE);

        $contentModifierMapSchema = new MapSchema(new CustomSchema(ContentModifierSchema::TYPE), new StringSchema('modifierName'));
        $contentModifierMapSchema->setDynamicOrder(true);

        return $contentModifierMapSchema;
    }
}
