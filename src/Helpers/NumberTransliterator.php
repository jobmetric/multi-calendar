<?php

namespace JobMetric\MultiCalendar\Helpers;

/**
 * ASCII ↔ Persian/Arabic-Indic digits translator.
 */
final class NumberTransliterator
{
    public static function trNum(string $str, string $mode = 'en', string $mf = '٫'): string
    {
        if ($mode === 'fa') {
            return strtr($str, [
                '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
                '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
                '.' => $mf, ',' => '٬',
            ]);
        }

        return strtr($str, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '٫' => '.', '٬' => ',', '،' => ',',
        ]);
    }
}
