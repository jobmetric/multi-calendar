<?php

namespace JobMetric\MultiCalendar\Tests\Converters;

use JobMetric\MultiCalendar\Converters\ChineseConverter;
use JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase;

class ChineseConverterTest extends BaseTestCase
{
    private ChineseConverter $conv;

    protected function setUp(): void
    {
        $this->requireIntlOrSkip();
        $this->conv = new ChineseConverter();
    }

    /**
     * @dataProvider \JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase::gregorianEdgeDatesProvider
     */
    public function testRoundTrip(int $y, int $m, int $d): void
    {
        $to = $this->conv->fromGregorian($y, $m, $d);
        $back = $this->conv->toGregorian(...$to);
        $this->assertSame([$y, $m, $d], $back, 'Chinese round-trip failed (check leap month handling).');
    }

    public function testFormattingConsistency(): void
    {
        [$cy, $cm, $cd] = $this->conv->fromGregorian(2025, 8, 13);
        $this->assertSame(\sprintf('%04d/%02d/%02d', $cy, $cm, $cd), $this->conv->fromGregorian(2025, 8, 13, '/'));
    }
}
