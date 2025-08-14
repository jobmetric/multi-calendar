<?php

namespace JobMetric\MultiCalendar\Converters;

final class EthiopianConverter extends AbstractCalendarConverter
{
    public static function key(): string
    {
        return 'ethiopian';
    }

    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string
    {
        return $this->convertCalendar('gregorian', $gy, $gm, $gd, 'ethiopic', $mod);
    }

    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string
    {
        return $this->convertCalendar('ethiopic', $y, $m, $d, 'gregorian', $mod);
    }
}
