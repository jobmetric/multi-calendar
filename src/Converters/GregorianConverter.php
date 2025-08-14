<?php

namespace JobMetric\MultiCalendar\Converters;

final class GregorianConverter extends AbstractCalendarConverter
{
    public static function key(): string
    {
        return 'gregorian';
    }

    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string
    {
        return $mod === '' ? [$gy, $gm, $gd] : sprintf('%04d%s%02d%s%02d', $gy, $mod, $gm, $mod, $gd);
    }

    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string
    {
        return $mod === '' ? [$y, $m, $d] : sprintf('%04d%s%02d%s%02d', $y, $mod, $m, $mod, $d);
    }
}
