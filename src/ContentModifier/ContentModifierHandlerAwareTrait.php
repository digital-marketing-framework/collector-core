<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

/** @phpstan-ignore-next-line This trait can be used by other packages, even though it is not used in this one. */
trait ContentModifierHandlerAwareTrait
{
    protected ContentModifierHandlerInterface $contentModifierHandler;

    public function setContentModifierHandler(ContentModifierHandlerInterface $contentModifierHandler): void
    {
        $this->contentModifierHandler = $contentModifierHandler;
    }
}
