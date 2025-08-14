<?php

namespace JobMetric\MultiCalendar\Tests\Converters;

use JobMetric\MultiCalendar\Converters\EthiopianConverter;
use JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase;

class EthiopianConverterTest extends BaseTestCase
{
    private EthiopianConverter $conv;

    protected function setUp(): void
    {
        $this->requireIntlOrSkip();
        $this->conv = new EthiopianConverter();
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
