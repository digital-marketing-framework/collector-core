<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierHandler;
use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierHandlerInterface;

trait ContentModifierHandlerRegistryTrait
{
    protected ContentModifierHandlerInterface $contentModifierHandler;

    public function getContentModifierHandler(): ContentModifierHandlerInterface
    {
        $this->contentModifierHandler ??= $this->createObject(ContentModifierHandler::class, [$this]);

        return $this->contentModifierHandler;
    }

    public function setContentModifierHandler(ContentModifierHandlerInterface $contentModifierHandler): void
    {
        $this->contentModifierHandler = $contentModifierHandler;
    }
}
