<?php

namespace JobMetric\MultiCalendar\Contracts;

interface CalendarConverterInterface
{
    /**
     * Convert from Gregorian to this calendar.
     * @return array{0:int,1:int,2:int}|string
     */
    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string;

    /**
     * Convert from this calendar to Gregorian.
     * @return array{0:int,1:int,2:int}|string
     */
    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string;

    /**
     * Canonical key for this calendar (e.g., 'jalali', 'hebrew', ...).
     */
    public static function key(): string;
}
