<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

interface ContentModifierHandlerAwareInterface
{
    public function setContentModifierHandler(ContentModifierHandlerInterface $contentModifierHandler): void;
}
