<?php

namespace DigitalMarketingFramework\Collector\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\AbstractModifierContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class MaskedContentResolver extends AbstractModifierContentResolver
{
    protected function modifyValue(string|ValueInterface|null &$result): void
    {
        if ($result !== null && $result !== '') {
            $result = GeneralUtility::maskValue((string)$result);
        }
    }
}
