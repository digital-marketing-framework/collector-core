<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Collector\Core\ConfigurationResolver\ContentResolver\MaskedContentResolver;
use DigitalMarketingFramework\Collector\Core\Tests\Integration\ConfigurationResolverRegistryTestTrait;
use DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver\AbstractModifierContentResolverTest;

/**
 * @covers MaskedContentResolver
 */
class MaskedContentResolverTest extends AbstractModifierContentResolverTest
{
    use ConfigurationResolverRegistryTestTrait;

    protected const KEYWORD = 'masked';

    public function modifyProvider(): array
    {
        return [
            [null,         null],
            ['',           ''],
            // ['abc',        'a****c'], // TODO currently results in "a****"
            ['abcd',       'a****d'],
            ['abcdefghij', 'ab****hij'],
            ['abcdefghij@klmnopq.rst', 'abcde****q.rst'],
            ['abc@defghijklmnopqrstuv.wxy', 'a****pqrstuv.wxy'],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [[], []],
            [
                ['', 'abcd', 'abcdefghij', 'abcdefghij@klmnopq.rst', 'abc@defghijklmnopqrstuv.wxy',],
                ['', 'a****d', 'ab****hij', 'abcde****q.rst', 'a****pqrstuv.wxy']],
        ];
    }
}
