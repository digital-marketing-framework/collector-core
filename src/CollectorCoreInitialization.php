<?php

namespace DigitalMarketingFramework\Collector\Core;

use DigitalMarketingFramework\Collector\Core\Backend\Controller\AjaxController\ContentModifierConfigurationEditorAjaxController;
use DigitalMarketingFramework\Collector\Core\DataProcessor\ValueModifier\MaskedValueModifier;
use DigitalMarketingFramework\Collector\Core\GlobalConfiguration\Schema\CollectorCoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\AjaxControllerInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;

class CollectorCoreInitialization extends Initialization
{
    protected const PLUGINS = [
        RegistryDomain::CORE => [
            ValueModifierInterface::class => [
                MaskedValueModifier::class,
            ],
            AjaxControllerInterface::class => [
                ContentModifierConfigurationEditorAjaxController::class,
            ],
        ],
    ];

    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(string $packageAlias = '')
    {
        parent::__construct('collector-core', '1.0.0', $packageAlias, new CollectorCoreGlobalConfigurationSchema());
    }
}
