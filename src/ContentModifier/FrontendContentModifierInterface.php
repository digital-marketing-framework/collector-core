<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface FrontendContentModifierInterface extends ContentModifierInterface
{
    /**
     * @return array<mixed>|false
     */
    public function getFrontendData(DataInterface $data): array|false;

    /**
     * @return array<string,mixed>
     */
    public function getFrontendSettings(): array;

    public function activateFrontendScripts(): void;
}
