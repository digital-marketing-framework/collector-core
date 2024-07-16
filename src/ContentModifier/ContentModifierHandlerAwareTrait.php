<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

trait ContentModifierHandlerAwareTrait
{
    protected ContentModifierHandlerInterface $contentModifierHandler;

    public function setContentModifierHandler(ContentModifierHandlerInterface $contentModifierHandler): void
    {
        $this->contentModifierHandler = $contentModifierHandler;
    }
}
