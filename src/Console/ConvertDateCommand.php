<?php

namespace JobMetric\MultiCalendar\Console;

use JobMetric\MultiCalendar\Factory\CalendarConverterFactory;
use InvalidArgumentException;
use DateTime;

class ConvertDateCommand
{
    public function execute(
        string $toCalendar = 'gregorian',
        string $formatOutput = 'Y-m-d',
        ?string $date = null,
        string $fromCalendar = 'gregorian',
        ?string $formatInput = null
    ): string {
        try {
            $parsedDate = $this->parseInputDate($date, $formatInput);
            
            $sourceYear = $parsedDate['year'];
            $sourceMonth = $parsedDate['month'];
            $sourceDay = $parsedDate['day'];
            $inputHasDay = $parsedDate['has_day'];

            if (strtolower($fromCalendar) === 'gregorian') {
                $gregorianYear = $sourceYear;
                $gregorianMonth = $sourceMonth;
                $gregorianDay = $sourceDay;
            } else {
                $delimiter = $this->detectDelimiter($formatInput ?? $date ?? '');
                $fromConverter = CalendarConverterFactory::make($fromCalendar);
                $gregorianRaw = $fromConverter->toGregorian($sourceYear, $sourceMonth, $sourceDay, $delimiter);
                
                [$gregorianYear, $gregorianMonth, $gregorianDay] = $this->extractDateTuple($gregorianRaw);
            }

            $toConverter = CalendarConverterFactory::make($toCalendar);
            $targetRaw = $toConverter->fromGregorian($gregorianYear, $gregorianMonth, $gregorianDay, $this->detectDelimiter($formatOutput));
            
            [$targetYear, $targetMonth, $targetDay] = $this->extractDateTuple($targetRaw);

            if (!$inputHasDay && $this->formatRequiresDay($formatOutput)) {
                $targetDay = 1;
            }

            return $this->formatDate($targetYear, $targetMonth, $targetDay, $formatOutput);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException("Error converting date: " . $e->getMessage());
        }
    }

    private function parseInputDate(?string $date, ?string $formatInput): array
    {
        if ($date === null || trim($date) === '') {
            $now = new DateTime();
            return [
                'year' => (int) $now->format('Y'),
                'month' => (int) $now->format('m'),
                'day' => (int) $now->format('d'),
                'has_day' => true
            ];
        }

        $date = trim($date);

        if ($formatInput !== null && trim($formatInput) !== '') {
            $formatInput = trim($formatInput);
            $dt = DateTime::createFromFormat('!' . $formatInput, $date);
            
            if ($dt !== false) {
                $errors = DateTime::getLastErrors();
                if ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                    return [
                        'year' => (int) $dt->format('Y'),
                        'month' => (int) $dt->format('m'),
                        'day' => (int) $dt->format('d'),
                        'has_day' => $this->checkFormatHasDay($formatInput)
                    ];
                }
            }
        }

        if (preg_match('/^(\d{1,4})[\/\.\-](\d{1,2})[\/\.\-](\d{1,2})$/', $date, $matches)) {
            return [
                'year' => (int) $matches[1],
                'month' => (int) $matches[2],
                'day' => (int) $matches[3],
                'has_day' => true
            ];
        }

        if (preg_match('/^(\d{1,4})[\/\.\-](\d{1,2})$/', $date, $matches)) {
            return [
                'year' => (int) $matches[1],
                'month' => (int) $matches[2],
                'day' => 1,
                'has_day' => false
            ];
        }

        if (preg_match('/^(\d{1,4})$/', $date, $matches)) {
            return [
                'year' => (int) $matches[1],
                'month' => 1,
                'day' => 1,
                'has_day' => false
            ];
        }

        $commonFormats = [
            'Y-m-d', 'Y/m/d', 'Y.m.d',
            'Y-m-d H:i:s', 'Y/m/d H:i:s', 'Y.m.d H:i:s',
            'd-m-Y', 'd/m/Y', 'd.m.Y',
            'm-d-Y', 'm/d/Y', 'm.d.Y',
        ];

        foreach ($commonFormats as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $date);
            if ($dt !== false) {
                return [
                    'year' => (int) $dt->format('Y'),
                    'month' => (int) $dt->format('m'),
                    'day' => (int) $dt->format('d'),
                    'has_day' => true
                ];
            }
        }

        throw new InvalidArgumentException("Invalid date format detected.");
    }

    private function extractDateTuple(array|string $result): array
    {
        if (is_array($result)) {
            if (count($result) >= 3) {
                return [(int)$result[0], (int)$result[1], (int)$result[2]];
            }
            throw new InvalidArgumentException("Converter returned invalid array structure.");
        }

        if (preg_match('/^(\d{1,4})[\/\.\-](\d{1,2})[\/\.\-](\d{1,2})$/', $result, $matches)) {
            return [(int)$matches[1], (int)$matches[2], (int)$matches[3]];
        }

        throw new InvalidArgumentException("Converter returned invalid string format.");
    }

    private function formatDate(int $year, int $month, int $day, string $format): string
    {
        $map = [
            'Y' => str_pad((string) $year, 4, '0', STR_PAD_LEFT),
            'm' => str_pad((string) $month, 2, '0', STR_PAD_LEFT),
            'd' => str_pad((string) $day, 2, '0', STR_PAD_LEFT),
        ];

        return strtr($format, $map);
    }

    private function detectDelimiter(string $text): string
    {
        if ($text === '') {
            return '';
        }
        
        if (preg_match('/[^Ymd0-9]/', $text, $matches)) {
            return $matches[0];
        }

        return '';
    }

    private function formatRequiresDay(string $format): bool
    {
        return strpos($format, 'd') !== false || strpos($format, 'D') !== false;
    }

    private function checkFormatHasDay(string $format): bool
    {
        return strpos($format, 'd') !== false || strpos($format, 'j') !== false;
    }
}