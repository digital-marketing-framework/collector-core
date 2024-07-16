<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface FrontendContentModifierInterface extends ContentModifierInterface
{
    /**
     * @return array<string,mixed>|false
     */
    public function getFrontendData(DataInterface $data): array|false;

    /**
     * @return array<string,mixed>
     */
    public function getFrontendSettings(): array;

    /**
     * @param array<string,mixed> $settings
     * @return array<string,mixed>
     */
    public function getContentSpecificFrontendSettings(string $id, array $settings): array;

    public function activateFrontendScripts(): void;
}
