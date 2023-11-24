<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

abstract class FrontendContentModifier extends ContentModifier implements FrontendContentModifierInterface
{
    abstract public function getFrontendData(): array|false;

    public function getFrontendSettings(): array
    {
        return [];
    }
}
