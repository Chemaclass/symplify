<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\RequireThisOnParentMethodCallRule;

/**
 * @extends RuleTestCase<RequireThisOnParentMethodCallRule>
 */
final class RequireThisOnParentMethodCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStaticallySameMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStaticallyWhenMethodOverriden.php', []];

        yield [
            __DIR__ . '/Fixture/CallParentMethodStatically.php',
            [[RequireThisOnParentMethodCallRule::ERROR_MESSAGE, 11], [
                RequireThisOnParentMethodCallRule::ERROR_MESSAGE,
                12,
            ]],
        ];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RequireThisOnParentMethodCallRule::class);
    }
}
