<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Plugin;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Collector\Core\SchemaDocument\Schema\Plugin\ContentModifier\ContentModifierSchema;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

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

    public function getFrontendContentModifier(ConfigurationInterface $configuration, string $contentModifierId): ?FrontendContentModifierInterface
    {
        $contentModifier = $this->getContentModifier($configuration, $contentModifierId);
        if ($contentModifier instanceof FrontendContentModifierInterface) {
            return $contentModifier;
        }

        return null;
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

        return array_filter($contentModifiers, static fn (ContentModifierInterface $contentModifier) => $contentModifier instanceof FrontendContentModifierInterface);
    }

    public function getContentModifierSchema(): SchemaInterface
    {
        $schema = new ContentModifierSchema();
        foreach ($this->getAllPluginClasses(ContentModifierInterface::class) as $key => $class) {
            $contentModifierSchema = $class::getSchema();
            $label = $class::getLabel();
            $schema->addItem($key, $contentModifierSchema, $label);
        }

        return $schema;
    }

    public function getContentModifiersSchema(SchemaDocument $schemaDocument): SchemaInterface
    {
        $contentModifierSchema = $this->getContentModifierSchema();
        $schemaDocument->addCustomType($contentModifierSchema, ContentModifierSchema::TYPE);

        $mapKeySchema = new StringSchema('modifierName');
        $mapKeySchema->getRenderingDefinition()->setLabel('Modifier Name');
        $mapValueSchema = new CustomSchema(ContentModifierSchema::TYPE);
        $mapValueSchema->getRenderingDefinition()->setLabel('{type} {../key}');
        $contentModifierMapSchema = new MapSchema($mapValueSchema, $mapKeySchema);
        $contentModifierMapSchema->getRenderingDefinition()->setIcon(Icon::CONTENT_MODIFIERS);
        $contentModifierMapSchema->setDynamicOrder(true);

        return $contentModifierMapSchema;
    }
}
