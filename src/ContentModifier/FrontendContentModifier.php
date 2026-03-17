<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Collector\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

abstract class FrontendContentModifier extends ContentModifier implements FrontendContentModifierInterface
{
    abstract public function getFrontendData(DataInterface $data, array $arguments): array|false;

    protected function getDefaultData(): mixed
    {
        return null;
    }

    public function passPermissionRequirementsToFrontend(): bool
    {
        return true;
    }

    public function getFrontendSettings(): array
    {
        $settings = [];

        if ($this->passPermissionRequirementsToFrontend()) {
            $requiredPermission = $this->getRequiredPermission();
            if ($requiredPermission !== '') {
                $settings['requiredPermission'] = $requiredPermission;
            }
        }

        $defaultData = $this->getDefaultData();
        if ($defaultData !== null) {
            $settings['defaultData'] = $defaultData;
        }

        return $settings;
    }

    public function getContentSpecificFrontendSettings(string $id, array $settings): array
    {
        return $settings;
    }

    public function activateFrontendScripts(): void
    {
    }

    public function getBackendSettingsSchema(SchemaDocument $schemaDocument): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getBackendSettingsSchema($schemaDocument);

        $markAsLoadingSchema = new BooleanSchema(false);
        $schema->addProperty('markAsLoading', $markAsLoadingSchema);

        return $schema;
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setIcon(Icon::FRONTEND_CONTENT_MODIFIER);

        return $schema;
    }
}
