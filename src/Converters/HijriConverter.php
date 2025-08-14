<?php

namespace JobMetric\MultiCalendar\Converters;

final class HijriConverter extends AbstractCalendarConverter
{
    public static function key(): string
    {
        return 'hijri';
    }

    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string
    {
        return $this->convertCalendar('gregorian', $gy, $gm, $gd, 'islamic', $mod);
    }

    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string
    {
        return $this->convertCalendar('islamic', $y, $m, $d, 'gregorian', $mod);
    }
}
