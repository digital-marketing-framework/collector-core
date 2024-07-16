<?php

namespace DigitalMarketingFramework\Collector\Core\Registry\Service;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierHandlerInterface;

interface ContentModifierHandlerRegistryInterface
{
    public function getContentModifierHandler(): ContentModifierHandlerInterface;

    public function setContentModifierHandler(ContentModifierHandlerInterface $contentModifierHandler): void;
}
