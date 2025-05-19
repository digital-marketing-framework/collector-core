<?php

namespace DigitalMarketingFramework\Collector\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendElementContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendFormContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendPageContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendPluginContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface as CollectorRegistryInterface;
use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\FullDocumentConfigurationEditorAjaxController;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class ContentModifierConfigurationEditorAjaxController extends FullDocumentConfigurationEditorAjaxController
{
    protected CollectorRegistryInterface $collectorRegistry;

    protected SchemaDocument $schemaDocument;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'content-modifier'
        );

        $this->collectorRegistry = $registry->getRegistryCollection()->getRegistryByClass(CollectorRegistryInterface::class);
    }

    /**
     * @return array{contentModifierList:bool,contentModifierInterface:class-string<ContentModifierInterface>}
     */
    protected function getSchemaDocumentConfiguration(): array
    {
        $parameters = $this->getParameters();

        return [
            'contentModifierList' => (bool)($parameters['contentModifierList'] ?? '0'),
            'contentModifierInterface' => match ($parameters['contentModifierGroup'] ?? '') {
                'plugin' => FrontendPluginContentModifierInterface::class,
                'page' => FrontendPageContentModifierInterface::class,
                'element' => FrontendElementContentModifierInterface::class,
                'form' => FrontendFormContentModifierInterface::class,
                default => throw new DigitalMarketingFrameworkException('Invalid content modifier group'),
            },
        ];
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        if (!isset($this->schemaDocument)) {
            $schemaDocumentConfig = $this->getSchemaDocumentConfiguration();

            $this->schemaDocument = $this->collectorRegistry->getContentModifierHandler()->getContentModifierBackendSettingsSchemaDocument(
                asList: $schemaDocumentConfig['contentModifierList'],
                contentModifierInterface: $schemaDocumentConfig['contentModifierInterface']
            );
        }

        return $this->schemaDocument;
    }
}
