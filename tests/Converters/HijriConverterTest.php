<?php

namespace JobMetric\MultiCalendar\Tests\Converters;

use JobMetric\MultiCalendar\Converters\HijriConverter;
use JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase;

class HijriConverterTest extends BaseTestCase
{
    private HijriConverter $conv;

    protected function setUp(): void
    {
        $this->requireIntlOrSkip();
        $this->conv = new HijriConverter();
    }

    /**
     * @dataProvider \JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase::gregorianEdgeDatesProvider
     */
    public function testRoundTrip(int $y, int $m, int $d): void
    {
        $to = $this->conv->fromGregorian($y, $m, $d);
        $back = $this->conv->toGregorian(...$to);
        $this->assertSame([$y, $m, $d], $back);
    }

    public function testFormatting(): void
    {
        [$hy, $hm, $hd] = $this->conv->fromGregorian(2025, 8, 13);
        $this->assertSame(\sprintf('%04d/%02d/%02d', $hy, $hm, $hd), $this->conv->fromGregorian(2025, 8, 13, '/'));
    }
}
