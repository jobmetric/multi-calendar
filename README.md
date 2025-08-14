[contributors-shield]: https://img.shields.io/github/contributors/jobmetric/multi-calendar.svg?style=for-the-badge
[contributors-url]: https://github.com/jobmetric/multi-calendar/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/jobmetric/multi-calendar.svg?style=for-the-badge&label=Fork
[forks-url]: https://github.com/jobmetric/multi-calendar/network/members
[stars-shield]: https://img.shields.io/github/stars/jobmetric/multi-calendar.svg?style=for-the-badge
[stars-url]: https://github.com/jobmetric/multi-calendar/stargazers
[license-shield]: https://img.shields.io/github/license/jobmetric/multi-calendar.svg?style=for-the-badge
[license-url]: https://github.com/jobmetric/multi-calendar/blob/master/LICENCE.md
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-blue.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/majidmohammadian

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

# Multi Calendar for PHP

Multi-Calendar is a PHP library for converting dates between multiple calendar systems, including Gregorian, Jalali (Persian), Hijri (Islamic), Hebrew, Buddhist, Coptic, Ethiopian, and Chinese.
It provides an easy-to-use API with support for both array and formatted string outputs, making date conversions reliable and consistent across different calendar types.

## Install via composer

Run the following command to pull in the latest version:
```bash
composer require jobmetric/multi-calendar
```

## Documentation

### Basic Usage

```php
use JobMetric\MultiCalendar\Converters\JalaliConverter;
use JobMetric\MultiCalendar\Converters\HijriConverter;

// Jalali (Persian) Example
$jalali = new JalaliConverter();
$result = $jalali->fromGregorian(2025, 8, 13); // [1404, 5, 22]
echo implode('/', $result); // "1404/05/22"

$gregorian = $jalali->toGregorian(1404, 5, 22); // [2025, 8, 13]
echo implode('-', $gregorian); // "2025-08-13"

// Hijri Example
$hijri = new HijriConverter();
echo $hijri->fromGregorian(2025, 8, 13, '/'); // e.g. "1447/02/20"
```

### Supported Calendars

This library supports the following calendar systems, each with its own converter class. You can instantiate these classes and use their methods to convert dates between the Gregorian calendar and the respective calendar system.

| Calendar Key       | Class Name                     | Description                                                   |
|--------------------|--------------------------------|---------------------------------------------------------------|
| `gregorian`        | GregorianConverter *(default)* | The standard calendar used worldwide.                         |
| `jalali` (Persian) | JalaliConverter                | The Iranian calendar, also known as the Solar Hijri calendar. |
| `hijri`  (Islamic) | HijriConverter                 | The lunar calendar used in the Islamic world.                 |
| `hebrew`           | HebrewConverter                | The calendar used in Jewish culture.                          |
| `buddhist`         | BuddhistConverter              | The calendar used in many Buddhist countries.                 |
| `coptic`           | CopticConverter                | The calendar used in the Coptic Orthodox Church.              |
| `ethiopian`        | EthiopianConverter             | The calendar used in Ethiopia.                                |
| `chinese`          | ChineseConverter               | The traditional Chinese calendar.                             |

### Using the Factory

You can also use the `CalendarConverterFactory` to create converters dynamically based on the calendar key:

```php
use JobMetric\MultiCalendar\Factory\CalendarConverterFactory;

// Create a converter dynamically
$conv = CalendarConverterFactory::make('jalali');

// Convert Gregorian to Jalali
echo $conv->fromGregorian(2025, 8, 13, '/'); // "1404/05/22"
```

### Number Transliteration

You can convert numbers between English, Persian, and Arabic numeral systems:

```php
use JobMetric\MultiCalendar\Helpers\NumberTransliterator;

echo NumberTransliterator::trNum('2025/08/13', 'fa'); // "۲۰۲۵/۰۸/۱۳"
echo NumberTransliterator::trNum('۱۴۰۴/۰۵/۲۲', 'en'); // "1404/05/22"
```

### Output Format

**Array output**: default when `$mod` (separator) is empty
Example: `[2025, 8, 13]`

**String output**: set `$mod` to a separator (`/`, `-`, `.`)
Example: `"2025/08/13"`

### Example Conversion Table for 2025-08-13 (Gregorian)

| Calendar  | Date       |
| --------- | ---------- |
| Gregorian | 2025-08-13 |
| Jalali    | 1404-05-22 |
| Hijri     | 1447-02-20 |
| Hebrew    | 5785-12-19 |
| Buddhist  | 2568-08-13 |
| Coptic    | 1741-12-07 |
| Ethiopian | 2017-12-07 |
| Chinese   | 2025-07-11 |

> **Note**: Values for non-Gregorian calendars are calculated using the `intl` extension. Actual results may vary slightly based on leap year and leap month handling.


## Contributing

Thank you for considering contributing to the Laravel Multi Calendar! The contribution guide can be found in the [CONTRIBUTING.md](https://github.com/jobmetric/multi-calendar/blob/master/CONTRIBUTING.md).

## License

The MIT License (MIT). Please see [License File](https://github.com/jobmetric/multi-calendar/blob/master/LICENCE.md) for more information.
