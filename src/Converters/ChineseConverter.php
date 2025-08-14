<?php

namespace JobMetric\MultiCalendar\Converters;

use IntlCalendar;
use InvalidArgumentException;

final class ChineseConverter extends AbstractCalendarConverter
{
    /** @var array<string,bool> Cache of leap-month flags keyed by "cy-cm-cd" */
    private array $leapCache = [];

    public static function key(): string
    {
        return 'chinese';
    }

    public function fromGregorian(int $gy, int $gm, int $gd, string $mod = ''): array|string
    {
        // Convert via ICU to Chinese and cache leap flag to preserve round-trip.
        $tz = $this->tz();
        $this->ensureIntl();

        $g = IntlCalendar::createInstance($tz, '@calendar=gregorian');
        if (!$g) {
            throw new InvalidArgumentException('Cannot create Gregorian calendar.');
        }
        $g->clear();
        $g->set(IntlCalendar::FIELD_YEAR, $gy);
        $g->set(IntlCalendar::FIELD_MONTH, $gm - 1);
        $g->set(IntlCalendar::FIELD_DAY_OF_MONTH, $gd);
        $g->set(IntlCalendar::FIELD_HOUR_OF_DAY, 0);
        $g->set(IntlCalendar::FIELD_MINUTE, 0);
        $g->set(IntlCalendar::FIELD_SECOND, 0);
        $g->set(IntlCalendar::FIELD_MILLISECOND, 0);

        $millis = $g->getTime();

        $c = IntlCalendar::createInstance($tz, '@calendar=chinese');
        $c->setTime($millis);

        $YEAR = defined('\IntlCalendar::FIELD_EXTENDED_YEAR') ? IntlCalendar::FIELD_EXTENDED_YEAR : IntlCalendar::FIELD_YEAR;
        $cy = $c->get($YEAR);
        $cm = $c->get(IntlCalendar::FIELD_MONTH) + 1;
        $cd = $c->get(IntlCalendar::FIELD_DAY_OF_MONTH);

        $isLeap = defined('\IntlCalendar::FIELD_IS_LEAP_MONTH') && (bool)$c->get(IntlCalendar::FIELD_IS_LEAP_MONTH);

        $this->leapCache["{$cy}-{$cm}-{$cd}"] = $isLeap;

        return $mod === '' ? [$cy, $cm, $cd] : sprintf('%04d%s%02d%s%02d', $cy, $mod, $cm, $mod, $cd);
    }

    public function toGregorian(int $y, int $m, int $d, string $mod = ''): array|string
    {
        // Try both normal and leap candidates; pick consistent one (using cache/round-trip).
        $tz = $this->tz();
        $this->ensureIntl();

        $YEAR = defined('\IntlCalendar::FIELD_EXTENDED_YEAR') ? IntlCalendar::FIELD_EXTENDED_YEAR : IntlCalendar::FIELD_YEAR;

        $candidate = function (bool $isLeap) use ($tz, $YEAR, $y, $m, $d): array {
            $from = IntlCalendar::createInstance($tz, '@calendar=chinese');
            if (!$from) {
                throw new InvalidArgumentException('Cannot create Chinese calendar.');
            }
            $from->clear();
            $from->set($YEAR, $y);
            $from->set(IntlCalendar::FIELD_MONTH, $m - 1);
            $from->set(IntlCalendar::FIELD_DAY_OF_MONTH, $d);
            $from->set(IntlCalendar::FIELD_HOUR_OF_DAY, 0);
            $from->set(IntlCalendar::FIELD_MINUTE, 0);
            $from->set(IntlCalendar::FIELD_SECOND, 0);
            $from->set(IntlCalendar::FIELD_MILLISECOND, 0);

            if (defined('\IntlCalendar::FIELD_IS_LEAP_MONTH')) {
                $from->set(IntlCalendar::FIELD_IS_LEAP_MONTH, $isLeap ? 1 : 0);
            }

            $millis = $from->getTime();

            $to = IntlCalendar::createInstance($tz, '@calendar=gregorian');
            $to->setTime($millis);

            $gy = $to->get(IntlCalendar::FIELD_YEAR);
            $gm = $to->get(IntlCalendar::FIELD_MONTH) + 1;
            $gd = $to->get(IntlCalendar::FIELD_DAY_OF_MONTH);

            return [$gy, $gm, $gd];
        };

        [$gy1, $gm1, $gd1] = $candidate(false);
        [$gy2, $gm2, $gd2] = $candidate(true);

        $key = "{$y}-{$m}-{$d}";
        if (isset($this->leapCache[$key])) {
            $preferLeap = $this->leapCache[$key] === true;
            [$G1, $G2] = [[$gy1, $gm1, $gd1], [$gy2, $gm2, $gd2]];
            $choice = $preferLeap ? $G2 : $G1;
            return $mod === '' ? $choice : sprintf('%04d%s%02d%s%02d', $choice[0], $mod, $choice[1], $mod, $choice[2]);
        }

        // Fallback when no cache is available: choose based on actual leapness via round-trip probe or pick later date.
        $leapOf = function (int $gy, int $gm, int $gd) use ($tz): bool {
            $g = IntlCalendar::createInstance($tz, '@calendar=gregorian');
            $g->clear();
            $g->set(IntlCalendar::FIELD_YEAR, $gy);
            $g->set(IntlCalendar::FIELD_MONTH, $gm - 1);
            $g->set(IntlCalendar::FIELD_DAY_OF_MONTH, $gd);
            $g->set(IntlCalendar::FIELD_HOUR_OF_DAY, 0);
            $g->set(IntlCalendar::FIELD_MINUTE, 0);
            $g->set(IntlCalendar::FIELD_SECOND, 0);
            $g->set(IntlCalendar::FIELD_MILLISECOND, 0);

            $millis = $g->getTime();
            $c = IntlCalendar::createInstance($tz, '@calendar=chinese');
            $c->setTime($millis);

            return defined('\IntlCalendar::FIELD_IS_LEAP_MONTH') && (bool)$c->get(IntlCalendar::FIELD_IS_LEAP_MONTH);
        };

        $leap1 = $leapOf($gy1, $gm1, $gd1);
        $leap2 = $leapOf($gy2, $gm2, $gd2);

        if ($leap1 !== $leap2) {
            $choice = $leap1 ? [$gy1, $gm1, $gd1] : [$gy2, $gm2, $gd2];
        } else {
            $dt1 = $gy1 * 10000 + $gm1 * 100 + $gd1;
            $dt2 = $gy2 * 10000 + $gm2 * 100 + $gd2;
            $choice = ($dt2 > $dt1) ? [$gy2, $gm2, $gd2] : [$gy1, $gm1, $gd1];
        }

        return $mod === '' ? $choice : sprintf('%04d%s%02d%s%02d', $choice[0], $mod, $choice[1], $mod, $choice[2]);
    }
}
