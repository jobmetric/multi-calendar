<?php

namespace JobMetric\MultiCalendar\Tests\Converters;

use JobMetric\MultiCalendar\Converters\JalaliConverter;
use JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase;

class JalaliConverterTest extends BaseTestCase
{
    private JalaliConverter $conv;

    protected function setUp(): void
    {
        $this->requireIntlOrSkip();
        $this->conv = new JalaliConverter();
    }

    public static function exactPairsProvider(): array
    {
        return [
            // [jy, jm, jd, gy, gm, gd]
            [1404, 5, 22, 2025, 8, 13],
            [1399, 12, 30, 2021, 3, 20],
            [1400, 1, 1, 2021, 3, 21],
            [1402, 12, 29, 2024, 3, 19],
        ];
    }

    /**
     * @dataProvider exactPairsProvider
     */
    public function testExactPairs(int $jy, int $jm, int $jd, int $gy, int $gm, int $gd): void
    {
        $this->assertSame([$gy, $gm, $gd], $this->conv->toGregorian($jy, $jm, $jd));
        $this->assertSame([$jy, $jm, $jd], $this->conv->fromGregorian($gy, $gm, $gd));
        $this->assertSame(\sprintf('%04d-%02d-%02d', $gy, $gm, $gd), $this->conv->toGregorian($jy, $jm, $jd, '-'));
        $this->assertSame(\sprintf('%04d/%02d/%02d', $jy, $jm, $jd), $this->conv->fromGregorian($gy, $gm, $gd, '/'));
    }

    public static function jalaliEdgeDatesProvider(): array
    {
        return [
            [1398, 12, 29],
            [1399, 12, 30],
            [1400, 1, 1],
            [1401, 12, 29],
            [1402, 1, 1],
            [1403, 12, 29],
            [1404, 5, 22],
        ];
    }

    /**
     * @dataProvider jalaliEdgeDatesProvider
     */
    public function testRoundTripJalali(int $jy, int $jm, int $jd): void
    {
        $g = $this->conv->toGregorian($jy, $jm, $jd);
        $j = $this->conv->fromGregorian(...$g);
        $this->assertSame([$jy, $jm, $jd], $j);
    }
}
