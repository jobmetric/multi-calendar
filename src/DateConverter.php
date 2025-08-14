<?php

namespace JobMetric\MultiCalendar;

use IntlCalendar;
use IntlTimeZone;
use InvalidArgumentException;

/**
 * Core calendar conversion utilities (no public calendar-specific methods).
 *
 * This class exposes protected low-level helpers used by concrete Converters:
 *  - convertCalendar(): generic ICU-based conversion between two calendars.
 *  - tz(): shared UTC timezone instance.
 *  - ensureIntl(): guard for ext-intl availability.
 *
 * Design:
 *  - Public API for calendar conversions is provided by Converters only.
 *  - Months in ICU are 0-based; this core accepts 1-based inputs and adjusts internally.
 *  - All calculations are done at UTC midnight to avoid DST/zone drift.
 */
class DateConverter
{
    /**
     * Ensure intl extension is loaded.
     */
    final protected function ensureIntl(): void
    {
        if (!\extension_loaded('intl')) {
            throw new InvalidArgumentException('The "intl" PHP extension is required.');
        }
    }

    /**
     * Shared UTC timezone for deterministic conversions.
     */
    final protected function tz(): IntlTimeZone
    {
        return IntlTimeZone::getGMT();
    }

    /**
     * Generic ICU calendar-to-calendar conversion.
     *
     * @param string $fromCal Source calendar keyword (gregorian, persian, islamic, hebrew, buddhist, coptic, ethiopic, chinese, dangi)
     * @param int    $y       Year (1-based)
     * @param int    $m       Month 1..12
     * @param int    $d       Day 1..31
     * @param string $toCal   Target calendar keyword
     * @param string $mod     Optional delimiter to return formatted string "YYYY{mod}MM{mod}DD"
     * @return array{0:int,1:int,2:int}|string
     */
    final protected function convertCalendar(
        string $fromCal,
        int $y,
        int $m,
        int $d,
        string $toCal,
        string $mod = ''
    ): array|string {
        $this->ensureIntl();

        $map = [
            'gregorian' => 'gregorian',
            'jalali'    => 'persian',
            'persian'   => 'persian',
            'hijri'     => 'islamic',
            'islamic'   => 'islamic',
            'hebrew'    => 'hebrew',
            'buddhist'  => 'buddhist',
            'coptic'    => 'coptic',
            'ethiopian' => 'ethiopic',
            'ethiopic'  => 'ethiopic',
            'chinese'   => 'chinese',
            'dangi'     => 'dangi',
        ];

        $from = $map[\strtolower($fromCal)] ?? null;
        $to   = $map[\strtolower($toCal)]   ?? null;

        if (!$from || !$to) {
            throw new InvalidArgumentException("Unsupported calendar. From='{$fromCal}' To='{$toCal}'");
        }

        $tz = $this->tz();

        // Chinese/Dangi use extended year in ICU.
        $useExtendedYearFrom = \in_array($from, ['chinese', 'dangi'], true) && \defined('\IntlCalendar::FIELD_EXTENDED_YEAR');
        $useExtendedYearTo   = \in_array($to,   ['chinese', 'dangi'], true) && \defined('\IntlCalendar::FIELD_EXTENDED_YEAR');

        $YEAR_FROM = $useExtendedYearFrom ? IntlCalendar::FIELD_EXTENDED_YEAR : IntlCalendar::FIELD_YEAR;
        $YEAR_TO   = $useExtendedYearTo   ? IntlCalendar::FIELD_EXTENDED_YEAR : IntlCalendar::FIELD_YEAR;

        $src = IntlCalendar::createInstance($tz, "@calendar={$from}");
        if (!$src) {
            throw new InvalidArgumentException("Cannot create source calendar '{$from}'.");
        }
        $src->clear();
        $src->set($YEAR_FROM, $y);
        $src->set(IntlCalendar::FIELD_MONTH, $m - 1);
        $src->set(IntlCalendar::FIELD_DAY_OF_MONTH, $d);
        $src->set(IntlCalendar::FIELD_HOUR_OF_DAY, 0);
        $src->set(IntlCalendar::FIELD_MINUTE, 0);
        $src->set(IntlCalendar::FIELD_SECOND, 0);
        $src->set(IntlCalendar::FIELD_MILLISECOND, 0);

        $millis = $src->getTime();

        $dst = IntlCalendar::createInstance($tz, "@calendar={$to}");
        if (!$dst) {
            throw new InvalidArgumentException("Cannot create target calendar '{$to}'.");
        }
        $dst->setTime($millis);

        $ty = $dst->get($YEAR_TO);
        $tm = $dst->get(IntlCalendar::FIELD_MONTH) + 1;
        $td = $dst->get(IntlCalendar::FIELD_DAY_OF_MONTH);

        return $mod === '' ? [$ty, $tm, $td] : \sprintf('%04d%s%02d%s%02d', $ty, $mod, $tm, $mod, $td);
    }
}
