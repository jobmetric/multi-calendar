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



## Command Line Tool

The package includes a command-line tool (`date-converter.sh`) that allows you to convert dates between different calendar systems directly from your terminal without writing PHP code.

### Usage

You can use the command-line tool in two ways:

**Direct execution:**
```bash
date-converter.sh [date] [options]
```

**Using Composer alias:**
```bash
composer date-convert -- [date] [options]
```

> **Note**: When using the Composer alias, use `--` to separate Composer arguments from the script arguments.

### Arguments

| Argument | Description |
|----------|-------------|
| `date` | Optional. If not provided, current date (now) will be used. |

### Options

| Option | Short | Description | Default |
|--------|-------|-------------|---------|
| `--date-format` | `-df` | Input calendar type (supported calendar systems) | `gregorian` |
| `--to` | `-t` | Output calendar type (supported calendar systems) | `gregorian` |
| `--format-input` | `-fi` | Input date string format (supported date formats), auto-detected if not provided | Auto-detect |
| `--format-output` | `-fo` | Output date format (supported date formats) | `Y-m-d` |
| `--timezone` | `-tz` | Convert time to specified timezone (e.g., Asia/Tehran, UTC, Europe/London) | None |
| `--from-timezone` | `-ftz` | Source timezone for time conversion (e.g., UTC, Europe/London) | Auto-detect |
| `--to-timezone` | `-ttz` | Target timezone for time conversion (e.g., Asia/Tehran, UTC) | None |
| `--help` | `-h` | Show this help message | - |

### Supported Calendar Systems

- `gregorian`
- `jalali` or `persian`
- `hijri` or `islamic`
- `hebrew`
- `buddhist`
- `coptic`
- `ethiopian` or `ethiopic`
- `chinese`

### Supported Date Formats

- `Y-m-d`
- `d/m/Y`
- `F d Y`
- `Y/m/d H:i:s` (with time)

### Timezone Conversion

The command-line tool supports timezone conversion in two ways:

1. **Simple timezone conversion** (using `--timezone` for backward compatibility):
   - Converts the input time to the specified timezone
   - Automatically detects the source timezone from the input date or uses default based on calendar type

2. **Explicit timezone conversion** (using `--from-timezone` and `--to-timezone`):
   - Allows you to explicitly specify both source and target timezones
   - More precise control over timezone conversion
   - Automatically adds time to output format if timezone conversion is performed

**Important Notes:**
- When timezone conversion is performed and the input contains time, the output format will automatically include time (`H:i:s`) if not already specified
- If no time is present in the input, timezone conversion will not add time to the output
- Supported timezone formats: All PHP supported timezone identifiers (e.g., `UTC`, `Asia/Tehran`, `Europe/London`, `America/New_York`, `Asia/Kabul`, `America/Sao_Paulo`)

**Timezone Conversion Examples:**

```bash
# Convert from UTC to Asia/Tehran
# Input: 2025-12-20 12:00:00 (UTC)
# Output: 2025/12/20 15:30:00 (Asia/Tehran, UTC+3:30)
date-converter.sh "2025-12-20 12:00:00" --from-timezone UTC --to-timezone Asia/Tehran

# Convert from Europe/London to Asia/Kabul
# Input: 2025-12-20 12:00:00 (London, UTC+0 in winter)
# Output: 2025/12/20 16:30:00 (Kabul, UTC+4:30)
date-converter.sh "2025-12-20 12:00:00" --from-timezone Europe/London --to-timezone Asia/Kabul

# Convert from America/Sao_Paulo to Asia/Tehran
# Input: 2025-12-20 12:00:00 (Brazil, UTC-3)
# Output: 2025/12/20 18:30:00 (Tehran, UTC+3:30)
date-converter.sh "2025-12-20 12:00:00" --from-timezone America/Sao_Paulo --to-timezone Asia/Tehran

# Convert calendar and timezone together (Gregorian to Jalali with timezone conversion)
# Input: 2025-12-20 12:00:00 (UTC)
# Output: 1404/09/29 15:30:00 (Jalali calendar, Asia/Tehran timezone)
date-converter.sh "2025-12-20 12:00:00" --to jalali --from-timezone UTC --to-timezone Asia/Tehran

# Convert calendar and timezone (Jalali to Gregorian with timezone conversion)
# Input: 1404-09-29 15:30:00 (Jalali, Asia/Tehran)
# Output: 2025/12/20 12:00:00 (Gregorian, UTC)
date-converter.sh "1404-09-29 15:30:00" --date-format jalali --to gregorian --from-timezone Asia/Tehran --to-timezone UTC
```

### Examples

**Direct execution:**
```bash
# Convert Gregorian to Jalali
date-converter.sh "2025-12-20" --to jalali --format-output Y/m/d

# Convert Jalali to Gregorian
date-converter.sh "1403-10-01" --date-format jalali --to gregorian

# Convert with custom input format
date-converter.sh "20/12/2025" --format-input "d/m/Y" --to jalali

# Convert with timezone (backward compatibility)
date-converter.sh "2025-12-20 12:00:00" --timezone Asia/Tehran

# Convert timezone from one timezone to another
date-converter.sh "2025-12-20 12:00:00" --from-timezone UTC --to-timezone Asia/Tehran --format-output "Y/m/d H:i:s"

# Convert timezone from London to Afghanistan
date-converter.sh "2025-12-20 12:00:00" --from-timezone Europe/London --to-timezone Asia/Kabul --format-output "Y/m/d H:i:s"

# Convert timezone from Brazil to Tehran
date-converter.sh "2025-12-20 12:00:00" --from-timezone America/Sao_Paulo --to-timezone Asia/Tehran --format-output "Y/m/d H:i:s"

# Convert calendar and timezone together (Gregorian to Jalali with timezone conversion)
date-converter.sh "2025-12-20 12:00:00" --to jalali --from-timezone UTC --to-timezone Asia/Tehran --format-output "Y/m/d H:i:s"

# Convert calendar and timezone (Jalali to Gregorian with timezone conversion)
date-converter.sh "1404-09-29 15:30:00" --date-format jalali --to gregorian --from-timezone Asia/Tehran --to-timezone UTC --format-output "Y/m/d H:i:s"
```

**Using Composer alias:**
```bash
# Convert Gregorian to Jalali
composer date-convert -- "2025-12-20" --to jalali --format-output Y/m/d

# Convert Jalali to Gregorian
composer date-convert -- "1403-10-01" --date-format jalali --to gregorian

# Convert with custom input format
composer date-convert -- "20/12/2025" --format-input "d/m/Y" --to jalali

# Convert timezone (backward compatibility)
composer date-convert -- "2025-12-20 12:00:00" --timezone Asia/Tehran

# Convert timezone from UTC to Asia/Tehran
composer date-convert -- "2025-12-20 12:00:00" --from-timezone UTC --to-timezone Asia/Tehran --format-output "Y/m/d H:i:s"

# Convert timezone from London to Afghanistan
composer date-convert -- "2025-12-20 12:00:00" --from-timezone Europe/London --to-timezone Asia/Kabul --format-output "Y/m/d H:i:s"

# Convert timezone from Brazil to Tehran
composer date-convert -- "2025-12-20 12:00:00" --from-timezone America/Sao_Paulo --to-timezone Asia/Tehran --format-output "Y/m/d H:i:s"

# Convert calendar and timezone together
composer date-convert -- "2025-12-20 12:00:00" --to jalali --from-timezone UTC --to-timezone Asia/Tehran --format-output "Y/m/d H:i:s"
```

## License

The MIT License (MIT). Please see [License File](https://github.com/jobmetric/multi-calendar/blob/master/LICENCE.md) for more information.

## Contributing

Thank you for considering contributing to the Laravel Multi Calendar! The contribution guide can be found in the [CONTRIBUTING.md](https://github.com/jobmetric/multi-calendar/blob/master/CONTRIBUTING.md).