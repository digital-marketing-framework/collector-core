<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

interface FrontendContentModifierInterface extends ContentModifierInterface
{
    /**
     * @return array<mixed>|false
     */
    public function getFrontendData(): array|false;

    /**
     * @return array<string,mixed>
     */
    public function getFrontendSettings(): array;
}
