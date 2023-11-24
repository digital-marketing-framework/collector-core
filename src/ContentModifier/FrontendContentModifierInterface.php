<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

interface FrontendContentModifierInterface extends ContentModifierInterface
{
    public function getFrontendData(): array|false;
    public function getFrontendSettings(): array;
}
