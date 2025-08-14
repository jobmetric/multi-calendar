<?php

namespace JobMetric\MultiCalendar\Tests\Factory;

use InvalidArgumentException;
use JobMetric\MultiCalendar\Factory\CalendarConverterFactory;
use JobMetric\MultiCalendar\Tests\TestCase\BaseTestCase;

class CalendarConverterFactoryTest extends BaseTestCase
{
    public function testMakeSupported(): void
    {
        $this->requireIntlOrSkip();

        $keys = ['gregorian','jalali','persian','hijri','islamic','hebrew','buddhist','coptic','ethiopian','ethiopic','chinese'];
        foreach ($keys as $k) {
            $conv = CalendarConverterFactory::make($k);
            $this->assertTrue(method_exists($conv, 'fromGregorian'));
            $this->assertTrue(method_exists($conv, 'toGregorian'));
        }
    }

    public function testMakeUnsupported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CalendarConverterFactory::make('unknown-calendar');
    }

    public function testBulkSweepAllConverters(): void
    {
        $this->requireIntlOrSkip();

        $samples = [
            [1990, 1, 1], [1990, 6, 15], [1990, 12, 31],
            [1996, 2, 29], [1997, 3, 1],
            [2001, 1, 31], [2001, 3, 31], [2001, 4, 30], [2001, 5, 31],
            [2016, 2, 29], [2016, 3, 1],
            [2020, 2, 29], [2020, 3, 1],
            [2024, 2, 29], [2024, 3, 1],
            [2025, 8, 13],
        ];

        $calendars = ['jalali','hijri','hebrew','buddhist','coptic','ethiopian','chinese'];

        foreach ($calendars as $key) {
            $conv = CalendarConverterFactory::make($key);
            foreach ($samples as [$y, $m, $d]) {
                $to = $conv->fromGregorian($y, $m, $d);
                $back = $conv->toGregorian(...$to);
                $this->assertSame([$y, $m, $d], $back, "{$key} round-trip mismatch @ {$y}-{$m}-{$d}");
            }
        }
    }
}
