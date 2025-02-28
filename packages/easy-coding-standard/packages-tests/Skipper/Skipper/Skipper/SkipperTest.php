<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper;

use Iterator;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\FifthElement;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\SixthSense;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\ThreeMan;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SkipperTest extends AbstractKernelTestCase
{
    private Skipper $skipper;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/config.php']);
        $this->skipper = $this->getService(Skipper::class);
    }

    /**
     * @dataProvider provideDataShouldSkipFileInfo()
     */
    public function testSkipFileInfo(string $filePath, bool $expectedSkip): void
    {
        $smartFileInfo = new SmartFileInfo($filePath);

        $resultSkip = $this->skipper->shouldSkipFileInfo($smartFileInfo);
        $this->assertSame($expectedSkip, $resultSkip);
    }

    /**
     * @return Iterator<string[]|bool[]>
     */
    public function provideDataShouldSkipFileInfo(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeRandom/file.txt', false];
        yield [__DIR__ . '/Fixture/SomeSkipped/any.txt', true];

        $basenameCwd = basename(getcwd());
        if ($basenameCwd === 'easy-coding-standard') {
            // split test inside packages/easy-coding-standard
            yield ['packages-tests/Skipper/Skipper/Skipper/Fixture/SomeSkipped/any.txt', true];
        } else {
            // from root symplify
            yield ['packages/easy-coding-standard/packages-tests/Skipper/Skipper/Skipper/Fixture/SomeSkipped/any.txt', true];
        }
    }

    /**
     * @param object|class-string $element
     * @dataProvider provideDataShouldSkipElement()
     */
    public function testSkipElement(string|object $element, bool $expectedSkip): void
    {
        $resultSkip = $this->skipper->shouldSkipElement($element);
        $this->assertSame($expectedSkip, $resultSkip);
    }

    /**
     * @return Iterator<bool[]|class-string<SixthSense>[]|class-string<ThreeMan>[]|FifthElement[]>
     */
    public function provideDataShouldSkipElement(): Iterator
    {
        yield [ThreeMan::class, false];
        yield [SixthSense::class, true];
        yield [new FifthElement(), true];
    }
}
