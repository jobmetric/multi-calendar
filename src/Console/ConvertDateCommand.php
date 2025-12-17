<?php

namespace JobMetric\MultiCalendar\Console;

use JobMetric\MultiCalendar\Factory\CalendarConverterFactory;
use InvalidArgumentException;

/**
 * Console command to convert current date to specified calendar format
 *
 * This class provides functionality to convert the current Gregorian date
 * to various calendar formats such as Jalali (Persian), Hijri (Islamic),
 * Hebrew, Buddhist, Coptic, Ethiopian, and Chinese calendars.
 *
 * @package JobMetric\MultiCalendar\Console
 * @author JobMetric
 * @since 1.0.0
 */
class ConvertDateCommand
{
    /**
     * Execute the command to convert current date to specified calendar format
     *
     * Takes the current system date (Gregorian) and converts it to the
     * specified calendar format with the given output format pattern.
     *
     * Supported calendar types:
     * - gregorian: Gregorian calendar (default Western calendar)
     * - jalali, persian: Persian/Jalali calendar
     * - hijri, islamic: Islamic/Hijri calendar
     * - hebrew: Hebrew calendar
     * - buddhist: Buddhist calendar
     * - coptic: Coptic calendar
     * - ethiopian, ethiopic: Ethiopian calendar
     * - chinese: Chinese calendar
     *
     * Format placeholders:
     * - Y: 4-digit year (e.g., 1403)
     * - m: 2-digit month (e.g., 01-12)
     * - d: 2-digit day (e.g., 01-31)
     *
     * @param string $calendar The target calendar type (jalali, hijri, gregorian, etc.)
     * @param string $format The output format pattern (default: 'Y-m-d')
     *                       Examples: 'Y-m-d', 'Y/m/d', 'Y.m.d'
     *
     * @return string The converted date in the specified format
     *
     * @throws InvalidArgumentException If the calendar type is not supported or conversion fails
     *
     * @example
     * $command = new ConvertDateCommand();
     * $result = $command->execute('jalali', 'Y/m/d');
     * // Returns: "1403/09/26" (example Jalali date)
     *
     * @example
     * $command = new ConvertDateCommand();
     * $result = $command->execute('hijri', 'Y-m-d');
     * // Returns: "1445-06-12" (example Hijri date)
     */
    public function execute(string $calendar, string $format = 'Y-m-d'): string
    {
        try {
            
            $now = new \DateTime();
            $year = (int) $now->format('Y');
            $month = (int) $now->format('m');
            $day = (int) $now->format('d');

            
            $converter = CalendarConverterFactory::make($calendar);

           
            $delimiter = '';
            if (preg_match('/[^Ymd]/', $format, $matches)) {
                $delimiter = $matches[0];
            }
            
           
            $result = $converter->fromGregorian($year, $month, $day, $delimiter);

            
            if (is_string($result)) {
                return $result;
            }

            
            if (is_array($result)) {
                [$y, $m, $d] = $result;
                
                $output = $format;
                $output = str_replace('Y', str_pad((string) $y, 4, '0', STR_PAD_LEFT), $output);
                $output = str_replace('m', str_pad((string) $m, 2, '0', STR_PAD_LEFT), $output);
                $output = str_replace('d', str_pad((string) $d, 2, '0', STR_PAD_LEFT), $output);
                
                return $output;
            }

            return (string) $result;
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException("Error converting date: " . $e->getMessage());
        }
    }
}

