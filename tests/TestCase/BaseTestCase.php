<?php

namespace JobMetric\MultiCalendar\Tests\TestCase;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected function requireIntlOrSkip(): void
    {
        if (!\extension_loaded('intl')) {
            $this->markTestSkipped('intl extension is not available; skipping ICU calendar tests.');
        }
    }

    /** Common set of Gregorian edge dates to stress conversions. */
    public static function gregorianEdgeDatesProvider(): array
    {
        return [
            [1600, 3, 1],
            [1700, 3, 1],
            [1800, 3, 1],
            [1899, 12, 31],
            [1900, 2, 28],
            [1900, 3, 1],
            [1969, 12, 31],
            [1970, 1, 1],
            [1999, 12, 31],
            [2000, 2, 29],
            [2004, 2, 29],
            [2016, 2, 29],
            [2019, 12, 31],
            [2020, 2, 29],
            [2024, 2, 29],
            [2025, 8, 13], // reference used earlier
            [2032, 2, 29],
        ];
    }
}
