<?php

namespace DigitalMarketingFramework\Collector\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ReferenceSchema;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class DataTransformationReferenceSchema extends ReferenceSchema
{
    protected function getDefaultFirstEmptyOptionLabel(): string
    {
        return 'Select Data Transformation';
    }

    protected function getReferencePath(): string
    {
        return sprintf('/%s/%s/*', CollectorConfigurationInterface::KEY_COLLECTOR, CollectorConfigurationInterface::KEY_DATA_TRANSFORMATIONS);
    }

    protected function getLabel(): string
    {
        return '{' . MapUtility::KEY_KEY . '}';
    }
}
