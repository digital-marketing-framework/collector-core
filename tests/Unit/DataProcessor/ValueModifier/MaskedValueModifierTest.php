<?php

namespace DigitalMarketingFramework\Collector\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Collector\Core\DataProcessor\ValueModifier\MaskedValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\ValueModifierTestBase;

class MaskedValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'masked';

    protected const CLASS_NAME = MaskedValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,         null],
        ['',           ''],
        // ['abc',        'a****c'], // TODO currently results in "a****"
        ['abcd',       'a****d'],
        ['abcdefghij', 'ab****hij'],
        ['abcdefghij@klmnopq.rst', 'abcde****q.rst'],
        ['abc@defghijklmnopqrstuv.wxy', 'a****pqrstuv.wxy'],

        [[], []],
        [
            ['', 'abcd', 'abcdefghij', 'abcdefghij@klmnopq.rst', 'abc@defghijklmnopqrstuv.wxy'],
            ['', 'a****d', 'ab****hij', 'abcde****q.rst', 'a****pqrstuv.wxy'],
        ],
    ];

    /**
     * @return array<array{string|array<string>|null,string|array<string>|null}>
     */
    public static function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
