<?php

namespace JobMetric\MultiCalendar\Tests\Converters;

use JobMetric\MultiCalendar\Converters\BuddhistConverter;
use JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase;

class BuddhistConverterTest extends BaseTestCase
{
    private BuddhistConverter $conv;

    protected function setUp(): void
    {
        $this->requireIntlOrSkip();
        $this->conv = new BuddhistConverter();
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
}
