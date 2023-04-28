<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Collector\Core\DataProcessor\ValueModifier\MaskedValueModifier;
use DigitalMarketingFramework\Collector\Core\Tests\Integration\DataProcessorRegistryTestTrait;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier\ValueModifierTest;
use DigitalMarketingFramework\Collector\Core\Tests\Unit\DataProcessor\ValueModifier\MaskedValueModifierTest as MaskedValueModifierUnitTest;

/**
 * @covers MaskedValueModifier
 */
class MaskedValueModifierTest extends ValueModifierTest
{
    use DataProcessorRegistryTestTrait;

    protected const KEYWORD = 'masked';
    
    public function modifyProvider(): array
    {
        return MaskedValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
