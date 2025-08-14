<?php

namespace JobMetric\MultiCalendar\Enum;

enum CalendarType: string
{
    case GREGORIAN = 'gregorian';
    case JALALI = 'jalali';
    case HIJRI = 'hijri';
    case HEBREW = 'hebrew';
    case BUDDHIST = 'buddhist';
    case COPTIC = 'coptic';
    case ETHIOPIAN = 'ethiopian';
    case CHINESE = 'chinese';
}
