<?php

namespace JobMetric\MultiCalendar\Converters;

final class CopticConverter extends AbstractCalendarConverter
{
    public static function key(): string
    {
        return 'coptic';
    }

    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string
    {
        return $this->convertCalendar('gregorian', $gy, $gm, $gd, 'coptic', $mod);
    }

    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string
    {
        return $this->convertCalendar('coptic', $y, $m, $d, 'gregorian', $mod);
    }
}
