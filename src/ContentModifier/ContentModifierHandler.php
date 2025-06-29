<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareTrait;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ConditionReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ConditionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use DigitalMarketingFramework\Core\Utility\ListUtility;

class ContentModifierHandler implements ContentModifierHandlerInterface, EndPointStorageAwareInterface, ConfigurationDocumentManagerAwareInterface
{
    use EndPointStorageAwareTrait;
    use ConfigurationDocumentManagerAwareTrait;

    /** @var array<string,array<string,array<array<string,mixed>>>> */
    protected array $contentSpecificSettings = [];

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function getContentSpecificSettings(): array
    {
        return $this->contentSpecificSettings;
    }

    protected function getEndPointContainerSchema(SchemaDocument $schemaDocument, string $contentModifierInterface): SwitchSchema
    {
        $endPointContainerSchema = new SwitchSchema('endPoint');
        // TODO label processing currently does not support nested path patterns
        // $endPointContainerSchema->getRenderingDefinition()->setLabel('End point: {type}, modifier: {config/{type}/type}');
        $endPointContainerSchema->getRenderingDefinition()->setLabel('End point: {type}');
        $endPointContainerSchema->getTypeSchema()->getRenderingDefinition()->setLabel('End point');

        $endPoints = $this->endPointStorage->fetchAll();
        foreach ($endPoints as $endPoint) {
            if (!$endPoint->getEnabled() || !$endPoint->getExposeToFrontend() || (!$endPoint->getPullEnabled() && !$endPoint->getPushEnabled())) {
                continue;
            }

            $schemaDocument->addValueToValueSet('endPoint/all', $endPoint->getName());

            if ($endPoint->getPullEnabled()) {
                $schemaDocument->addValueToValueSet('endPoint/collector', $endPoint->getName());
            }

            if ($endPoint->getPushEnabled()) {
                $schemaDocument->addValueToValueSet('endPoint/distributor', $endPoint->getName());
            }

            $endPointSchema = new SwitchSchema('endPoint/' . $endPoint->getName() . '/contentModifier');
            $endPointSchema->getTypeSchema()->getRenderingDefinition()->setLabel('Content Modifier');

            $configStack = $this->configurationDocumentManager->getConfigurationStackFromDocument($endPoint->getConfigurationDocument());
            $configuration = new CollectorConfiguration($configStack);
            $contentModifiers = $this->registry->getFrontendContentModifiers($configuration);

            foreach ($contentModifiers as $contentModifier) {
                if (!$contentModifier instanceof $contentModifierInterface) {
                    continue;
                }

                $modifierSchema = $contentModifier->getBackendSettingsSchema($schemaDocument);
                $endPointSchema->addItem(
                    $contentModifier->getContentModifierId(),
                    $modifierSchema,
                    label: $contentModifier->getKeyword() . ': ' . $contentModifier->getContentModifierName()
                );
            }

            $endPointContainerSchema->addItem($endPoint->getName(), $endPointSchema);
        }

        return $endPointContainerSchema;
    }

    public function getContentModifierBackendSettingsSchemaDocument(bool $asList, string $contentModifierInterface): SchemaDocument
    {
        $schemaDocument = new SchemaDocument();

        // complex values
        $schemaDocument->addCustomType(new ValueSchema(), ValueSchema::TYPE);
        $schemaDocument->addCustomType($this->registry->getValueSourceSchema(), ValueSourceSchema::TYPE);
        $schemaDocument->addCustomType($this->registry->getValueModifierSchema(), ValueModifierSchema::TYPE);

        // complex conditions
        $schemaDocument->addCustomType($this->registry->getConditionSchema(), ConditionSchema::TYPE);
        $schemaDocument->addCustomType($this->registry->getConditionSchema(withContext: true), ConditionSchema::TYPE_WITH_CONTEXT);
        $schemaDocument->addCustomType(new ConditionReferenceSchema(), ConditionReferenceSchema::TYPE);
        $schemaDocument->addCustomType($this->registry->getComparisonSchema(), ComparisonSchema::TYPE);

        // end point/content modification settings
        $endPointContainerSchema = $this->getEndPointContainerSchema($schemaDocument, $contentModifierInterface);

        if ($asList) {
            $configSchema = new ListSchema($endPointContainerSchema);
            $configSchema->getRenderingDefinition()->setLabel('Content Modifiers');
        } else {
            $configSchema = $endPointContainerSchema;
        }

        $schemaDocument->getMainSchema()->getRenderingDefinition()->setLabel('Settings');
        $schemaDocument->getMainSchema()->addProperty('settings', $configSchema);

        return $schemaDocument;
    }

    public function setContentSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): void
    {
        $contentModifierListSetup = $this->getContentModifierSetupFromConfigurationDocument($configurationDocument, $asList);
        foreach ($contentModifierListSetup as $contentModifierSetup) {
            $contentModifier = $contentModifierSetup['contentModifier'];
            $endPoint = $contentModifierSetup['endPoint'];
            $publicKey = $contentModifier->getPublicKey($endPoint);
            $this->contentSpecificSettings[$publicKey][$id][] = $contentModifier->getContentSpecificFrontendSettings($id, $contentModifierSetup['settings']);
            $contentModifier->activateFrontendScripts();
        }
    }

    public function setElementSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): void
    {
        $this->setContentSpecificSettingsFromConfigurationDocument($configurationDocument, $asList, $id);
    }

    public function setPageSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList): void
    {
        $this->setContentSpecificSettingsFromConfigurationDocument($configurationDocument, $asList, '<page>');
    }

    public function setFormSpecificSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): void
    {
        $this->setContentSpecificSettingsFromConfigurationDocument($configurationDocument, $asList, $id);
    }

    /**
     * @param array<array<string,mixed>> $settings
     *
     * @return array<array{endPoint:EndPointInterface,contentModifier:FrontendContentModifierInterface,settings:array<string,mixed>}>
     */
    protected function produceContentModifierListWithContentSpecificBackendSettings(array $settings): array
    {
        $endPoints = [];
        $endPointConfigurations = [];
        $result = [];
        foreach ($settings as $settingsItem) {
            $endPointName = SwitchSchema::getSwitchType($settingsItem);
            $endPointConfig = SwitchSchema::getSwitchConfiguration($settingsItem);

            if (isset($endPoints[$endPointName])) {
                $endPoint = $endPoints[$endPointName];
                $endPointConfiguration = $endPointConfigurations[$endPointName];
            } else {
                $endPoint = $this->endPointStorage->fetchByName($endPointName);
                if (!$endPoint instanceof EndPointInterface) {
                    throw new DigitalMarketingFrameworkException(sprintf('End point with name "%s" not found.', $endPointName));
                }

                $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromDocument($endPoint->getConfigurationDocument());
                $endPointConfiguration = new Configuration($configurationStack);

                $endPoints[$endPointName] = $endPoint;
                $endPointConfigurations[$endPointName] = $endPointConfiguration;
            }

            $contentModifierId = SwitchSchema::getSwitchType($endPointConfig);
            $contentModifierSettings = SwitchSchema::getSwitchConfiguration($endPointConfig);

            $contentModifier = $this->registry->getFrontendContentModifier($endPointConfiguration, $contentModifierId);

            if (!$contentModifier instanceof ContentModifierInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('Content modifier with id "%s" not.', $contentModifierId));
            }

            $result[] = [
                'endPoint' => $endPoint,
                'contentModifier' => $contentModifier,
                'settings' => $contentModifierSettings,
            ];
        }

        return $result;
    }

    public function getContentModifierWithContentSpecificBackendSettings(array $settings): array
    {
        $settingsList = [$settings['settings'] ?? []];
        $result = $this->produceContentModifierListWithContentSpecificBackendSettings($settingsList);

        return reset($result);
    }

    public function getContentModifierListWithContentSpecificBackendSettings(array $settings): array
    {
        $settingsList = ListUtility::flatten($settings['settings'] ?? []);

        return $this->produceContentModifierListWithContentSpecificBackendSettings($settingsList);
    }

    /**
     * @return array<array{endPoint:EndPointInterface,contentModifier:FrontendContentModifierInterface,settings:array<string,mixed>}>
     */
    protected function getContentModifierSetupFromConfigurationDocument(string $configurationDocument, bool $asList): array
    {
        $settings = $this->configurationDocumentManager->getParser()->parseDocument($configurationDocument);
        if ($asList) {
            return $this->getContentModifierListWithContentSpecificBackendSettings($settings);
        }

        return [$this->getContentModifierWithContentSpecificBackendSettings($settings)];
    }

    public function getContentSpecificFrontendSettingsFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): array
    {
        $contentModifierListSetup = $this->getContentModifierSetupFromConfigurationDocument($configurationDocument, $asList);

        $settings = [];
        foreach ($contentModifierListSetup as $setup) {
            $contentModifier = $setup['contentModifier'];
            $endPoint = $setup['endPoint'];
            $contentSettings = $contentModifier->getContentSpecificFrontendSettings($id, $setup['settings']);
            $contentSettings['plugin'] = $contentModifier->getPublicKey($endPoint);
            foreach ($contentSettings as $key => $value) {
                if (is_scalar($value)) {
                    if (isset($settings[$key])) {
                        $valueList = explode(',', (string)$settings[$key]);
                        if (!in_array($value, $valueList)) {
                            $valueList[] = $value;
                            $settings[$key] = implode(',', $valueList);
                        }
                    } else {
                        $settings[$key] = $value;
                    }
                } else {
                    if (isset($settings[$key])) {
                        throw new DigitalMarketingFrameworkException('Settings with the same key must be scalar (' . $key . ')');
                    }

                    $settings[$key] = $value;
                }
            }
        }

        return $settings;
    }

    public function getDataAttributesFromConfigurationDocument(string $configurationDocument, bool $asList, string $id): array
    {
        $settings = $this->getContentSpecificFrontendSettingsFromConfigurationDocument($configurationDocument, $asList, $id);
        $prefix = 'dmf'; // TODO fetch attribute prefix from global settings

        $attributes = [];
        foreach ($settings as $key => $value) {
            if (!is_scalar($value)) {
                $value = json_encode($value, flags: JSON_THROW_ON_ERROR);
            }

            $attributes[sprintf('data-%s-%s', $prefix, GeneralUtility::camelCaseToDashed($key))] = $value;
        }

        return $attributes;
    }

    public function renderFromConfigurationDocument(string $configurationDocument, bool $asList, ?string $id = null): string
    {
        $result = '';

        try {
            $contentModifierSetup = $this->getContentModifierSetupFromConfigurationDocument($configurationDocument, $asList);
            foreach ($contentModifierSetup as $setup) {
                $contentModifier = $setup['contentModifier'];
                $endPoint = $setup['endPoint'];
                $settings = $setup['settings'];
                if ($id !== null) {
                    $settings['contentId'] = $id;
                }

                $contentModifier->activateFrontendScripts();
                $result .= $contentModifier->render($endPoint, $settings);
            }
        } catch (DigitalMarketingFrameworkException $e) {
            $result = $this->registry->renderErrorMessage($e->getMessage());
        }

        return $result;
    }
}
