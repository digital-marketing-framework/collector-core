<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface CollectorConfigurationInterface extends ConfigurationInterface
{
    public const KEY_COLLECTOR = 'collector';

    public const KEY_DATA_COLLECTORS = 'collectors';

    public const KEY_DATA_TRANSFORMATIONS = 'dataTransformations';

    /**
     * @return array<string,mixed>
     */
    public function getCollectorConfiguration(): array;

    public function dataCollectorExists(string $dataCollectorName): bool;

    /**
     * @return array<string,mixed>
     */
    public function getDataCollectorConfiguration(string $dataCollectorName): array;

    public function dataTransformationExists(string $transformationname): bool;

    /**
     * @return array<string,mixed>
     */
    public function getDataTransformationConfiguration(string $transformationName): array;
}
