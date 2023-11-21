<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Configuration;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface CollectorConfigurationInterface extends ConfigurationInterface
{
    public const KEY_COLLECTOR = 'collector';

    public const KEY_DATA_COLLECTORS = 'collectors';

    public const KEY_DATA_TRANSFORMATIONS = 'dataTransformations';

    public const KEY_DEFAULT_DATA_TRANSFORMATION = 'defaultTransformation';

    public const KEY_CONTENT_MODIFIERS = 'contentModifiers';

    /**
     * @return array<string,mixed>
     */
    public function getCollectorConfiguration(): array;

    public function dataCollectorExists(string $dataCollectorName): bool;

    /**
     * @return array<string,mixed>
     */
    public function getDataCollectorConfiguration(string $dataCollectorName): array;

    public function getDataTransformationName(string $transformationId): ?string;

    public function dataTransformationExists(string $transformatioName): bool;

    /**
     * @return array<string,mixed>
     */
    public function getDataTransformationConfiguration(string $transformationName): array;

    public function getDefaultDataTransformationName(): string;

    public function contentModifierExists(string $keyword): bool;

    /**
     * @return array<string,mixed>
     */
    public function getContentModifierConfiguration(string $keyword): array;
}
