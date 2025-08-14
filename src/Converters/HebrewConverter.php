<?php

namespace JobMetric\MultiCalendar\Converters;

final class HebrewConverter extends AbstractCalendarConverter
{
    public static function key(): string
    {
        return 'hebrew';
    }

    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string
    {
        return $this->convertCalendar('gregorian', $gy, $gm, $gd, 'hebrew', $mod);
    }

    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string
    {
        return $this->convertCalendar('hebrew', $y, $m, $d, 'gregorian', $mod);
    }
}
