<?php

namespace JobMetric\MultiCalendar\Converters;

final class BuddhistConverter extends AbstractCalendarConverter
{
    public static function key(): string
    {
        return 'buddhist';
    }

    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string
    {
        return $this->convertCalendar('gregorian', $gy, $gm, $gd, 'buddhist', $mod);
    }

    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string
    {
        return $this->convertCalendar('buddhist', $y, $m, $d, 'gregorian', $mod);
    }
}
