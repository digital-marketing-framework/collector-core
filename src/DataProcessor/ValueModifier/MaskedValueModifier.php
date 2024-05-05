<?php

namespace DigitalMarketingFramework\Collector\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class MaskedValueModifier extends ValueModifier
{
    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value !== null && (string)$value !== '') {
            return GeneralUtility::maskValue((string)$value);
        }

        return $value;
    }
}
