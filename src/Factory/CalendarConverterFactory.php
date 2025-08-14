<?php

namespace JobMetric\MultiCalendar\Factory;

use InvalidArgumentException;
use JobMetric\MultiCalendar\Contracts\CalendarConverterInterface;
use JobMetric\MultiCalendar\Converters\{BuddhistConverter,
    ChineseConverter,
    CopticConverter,
    EthiopianConverter,
    GregorianConverter,
    HebrewConverter,
    HijriConverter,
    JalaliConverter};

final class CalendarConverterFactory
{
    public static function make(string $calendarKey): CalendarConverterInterface
    {
        return match (\strtolower($calendarKey)) {
            'gregorian' => new GregorianConverter,
            'jalali', 'persian' => new JalaliConverter,
            'hijri', 'islamic' => new HijriConverter,
            'hebrew' => new HebrewConverter,
            'buddhist' => new BuddhistConverter,
            'coptic' => new CopticConverter,
            'ethiopian', 'ethiopic' => new EthiopianConverter,
            'chinese' => new ChineseConverter,
            default => throw new InvalidArgumentException("Unsupported calendar: {$calendarKey}"),
        };
    }
}
