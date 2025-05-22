<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Collector\Core\Tests\Integration\DataProcessor\DataProcessorPluginTestTrait;
use DigitalMarketingFramework\Collector\Core\Tests\Unit\DataProcessor\ValueModifier\MaskedValueModifierTest as MaskedValueModifierUnitTest;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier\ValueModifierTestBase;

/**
 * @covers \DigitalMarketingFramework\Collector\Core\DataProcessor\ValueModifier\MaskedValueModifier
 */
class MaskedValueModifierTest extends ValueModifierTestBase
{
    use DataProcessorPluginTestTrait;

    protected const KEYWORD = 'masked';

    /**
     * @return array<array{string|array<string>|null,string|array<string>|null}>
     */
    public static function modifyProvider(): array
    {
        return MaskedValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
