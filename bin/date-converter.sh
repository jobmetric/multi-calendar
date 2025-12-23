#!/bin/bash

print_help() {
  echo ""
  echo "Description:"
  echo "  This script converts dates between different calendar systems."
  echo "  It can also convert dates between different date formats."
  echo "  Supported calendar systems:"
  echo "    - gregorian"
  echo "    - jalali or persian"
  echo "    - hijri or islamic"
  echo "    - hebrew"
  echo "    - buddhist"
  echo "    - coptic"
  echo "    - ethiopian or ethiopic"
  echo "    - chinese"
  echo "  Supported date formats:"
  echo "    - Y-m-d"
  echo "    - d/m/Y"
  echo "    - F d Y"
  echo ""
  echo "Usage:"
  echo "  $0 [date] [options]"
  echo ""
  echo "Arguments:"
  echo "  date                      Optional. If not provided, current date (now) will be used."
  echo ""
  echo "Options:"
  echo "  -df, --date-format        Input calendar type (supported calendar systems). Default: gregorian"
  echo "  -t,  --to                 Output calendar type (supported calendar systems). Default: gregorian"
  echo "  -fi, --format-input       Input date string format (supported date formats), auto-detected if not provided"
  echo "  -fo, --format-output      Output date format (supported date formats). Default: Y-m-d"
  echo "  -h,  --help               Show this help message"
  echo ""
  echo "Examples:"
  echo "  $0 \"2025-12-20\" --to jalali --format-output Y/m/d"
  echo "  $0 \"1403-10-01\" --date-format jalali --to gregorian"
  echo "  $0 \"20/12/2025\" --format-input \"d/m/Y\" --to jalali"
  echo "  $0 \"29/09/1404\" --date-format jalali --format-input \"d/m/Y\" --to gregorian"
}

# Defaults
DATE=""
DATE_FORMAT="gregorian"
TO="gregorian"
FORMAT_INPUT=""
FORMAT_OUTPUT="Y-m-d"


# Parse args
POSITIONAL=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    --help)
      print_help
      exit 0
      ;;
    -df|--date-format)
      DATE_FORMAT="${2:-}"
      shift 2
      ;;
    --date-format=*)
      DATE_FORMAT="${1#*=}"
      shift
      ;;
    -t|--to)
      TO="${2:-}"
      shift 2
      ;;
    --to=*)
      TO="${1#*=}"
      shift
      ;;
    -fi|--format-input)
      FORMAT_INPUT="${2:-}"
      shift 2
      ;;
    --format-input=*)
      FORMAT_INPUT="${1#*=}"
      shift
      ;;
    -fo|--format-output)
      FORMAT_OUTPUT="${2:-}"
      shift 2
      ;;
    --format-output=*)
      FORMAT_OUTPUT="${1#*=}"
      shift
      ;;
    -h|--help)
      print_help
      exit 0
      ;;
    -*)
      echo "Error: Unknown option: $1" >&2
      print_help >&2
      exit 1
      ;;
    *)
      POSITIONAL+=("$1")
      shift
      ;;
  esac
done

set -- "${POSITIONAL[@]}"
DATE="${1:-}"


# Basic validation

if [[ -z "$DATE_FORMAT" ]]; then
  echo "Error: --date-format requires a value" >&2
  exit 1
fi

if [[ -z "$TO" ]]; then
  echo "Error: --to requires a value" >&2
  exit 1
fi

if [[ -z "$FORMAT_OUTPUT" ]]; then
  echo "Error: --format-output requires a value" >&2
  exit 1
fi

# Check if PHP is available
if ! command -v php &> /dev/null; then
  echo "Error: PHP is not installed or not in PATH" >&2
  exit 1
fi

# Find autoload.php
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PACKAGE_DIR="$(dirname "$SCRIPT_DIR")"

AUTOLOAD_FILE=""
if [ -f "$PACKAGE_DIR/vendor/autoload.php" ]; then
  AUTOLOAD_FILE="$PACKAGE_DIR/vendor/autoload.php"
elif [ -f "$PACKAGE_DIR/../../vendor/autoload.php" ]; then
  AUTOLOAD_FILE="$PACKAGE_DIR/../../vendor/autoload.php"
else
  CURRENT_DIR="$PACKAGE_DIR"
  while [ "$CURRENT_DIR" != "/" ]; do
    if [ -f "$CURRENT_DIR/composer.json" ] && [ -f "$CURRENT_DIR/vendor/autoload.php" ]; then
      AUTOLOAD_FILE="$CURRENT_DIR/vendor/autoload.php"
      break
    fi
    CURRENT_DIR="$(dirname "$CURRENT_DIR")"
  done
fi

if [ -z "$AUTOLOAD_FILE" ] || [ ! -f "$AUTOLOAD_FILE" ]; then
  echo "Error: vendor/autoload.php not found. Please run 'composer install' first." >&2
  exit 1
fi


# Execute PHP (safe env passing)
AUTOLOAD_FILE="$AUTOLOAD_FILE" \
MULTI_CALENDAR_DATE="$DATE" \
MULTI_CALENDAR_FROM_CALENDAR="$DATE_FORMAT" \
MULTI_CALENDAR_TO_CALENDAR="$TO" \
MULTI_CALENDAR_FORMAT_INPUT="$FORMAT_INPUT" \
MULTI_CALENDAR_FORMAT_OUTPUT="$FORMAT_OUTPUT" \
php -r '
require_once getenv("AUTOLOAD_FILE");

use JobMetric\MultiCalendar\Factory\CalendarConverterFactory;

function parseInputDate(?string $date, ?string $formatInput): array
{
    if ($date === null || trim($date) === "") {
        $now = new DateTime();
        return [
            "year" => (int) $now->format("Y"),
            "month" => (int) $now->format("m"),
            "day" => (int) $now->format("d"),
            "has_day" => true
        ];
    }

    $date = trim($date);

    if ($formatInput !== null && trim($formatInput) !== "") {
        $formatInput = trim($formatInput);
        $dt = DateTime::createFromFormat("!" . $formatInput, $date);
        
        if ($dt !== false) {
            $errors = DateTime::getLastErrors();
            if ($errors === false || ($errors["warning_count"] === 0 && $errors["error_count"] === 0)) {
                $year = (int) $dt->format("Y");
                $year_str = $dt->format("Y");
                
                if ($year < 1 || $year > 9999 || (strlen($year_str) > 1 && $year_str[0] === "0")) {
                    throw new InvalidArgumentException("Invalid year value in date.");
                }
                
                return [
                    "year" => $year,
                    "month" => (int) $dt->format("m"),
                    "day" => (int) $dt->format("d"),
                    "has_day" => checkFormatHasDay($formatInput)
                ];
            }
        }
        
        $errorMsg = sprintf("Invalid date format. Date '%s' does not match format '%s'.", $date, $formatInput);
        throw new InvalidArgumentException($errorMsg);
    }

    if (preg_match("/^(\d{1,4})[\/\.\-](\d{1,2})[\/\.\-](\d{1,2})$/", $date, $matches)) {
        $year_str = $matches[1];
        $year = (int) $year_str;
        
        if ($year < 1 || $year > 9999 || (strlen($year_str) > 1 && $year_str[0] === "0")) {
            throw new InvalidArgumentException("Invalid year value in date.");
        }
        
        $month = (int) $matches[2];
        $day = (int) $matches[3];
        
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
            throw new InvalidArgumentException("Invalid month/day values in date.");
        }
        
        return [
            "year" => $year,
            "month" => $month,
            "day" => $day,
            "has_day" => true
        ];
    }

    if (preg_match("/^(\d{1,4})[\/\.\-](\d{1,2})$/", $date, $matches)) {
        $year_str = $matches[1];
        $year = (int) $year_str;
        
        if ($year < 1 || $year > 9999 || (strlen($year_str) > 1 && $year_str[0] === "0")) {
            throw new InvalidArgumentException("Invalid year value in date.");
        }
        
        $month = (int) $matches[2];
        
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException("Invalid month value in date.");
        }
        
        return [
            "year" => $year,
            "month" => $month,
            "day" => 1,
            "has_day" => false
        ];
    }

    if (preg_match("/^(\d{1,4})$/", $date, $matches)) {
        $year_str = $matches[1];
        $year = (int) $year_str;
        
        if ($year < 1 || $year > 9999 || (strlen($year_str) > 1 && $year_str[0] === "0")) {
            throw new InvalidArgumentException("Invalid year value in date.");
        }
        
        return [
            "year" => $year,
            "month" => 1,
            "day" => 1,
            "has_day" => false
        ];
    }

    $commonFormats = [
        "Y-m-d", "Y/m/d", "Y.m.d",
        "Y-m-d H:i:s", "Y/m/d H:i:s", "Y.m.d H:i:s",
        "d-m-Y", "d/m/Y", "d.m.Y",
        "m-d-Y", "m/d/Y", "m.d.Y",
    ];

    foreach ($commonFormats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $date);
        if ($dt !== false) {
            return [
                "year" => (int) $dt->format("Y"),
                "month" => (int) $dt->format("m"),
                "day" => (int) $dt->format("d"),
                "has_day" => true
            ];
        }
    }

    throw new InvalidArgumentException("Invalid date format detected.");
}

function extractDateTuple(array|string $result): array
{
    if (is_array($result)) {
        if (count($result) >= 3) {
            return [(int)$result[0], (int)$result[1], (int)$result[2]];
        }
        throw new InvalidArgumentException("Converter returned invalid array structure.");
    }

    if (preg_match("/^(\d{1,4})[^0-9](\d{1,2})[^0-9](\d{1,2})$/", $result, $matches)) {
        return [(int)$matches[1], (int)$matches[2], (int)$matches[3]];
    }

    throw new InvalidArgumentException("Converter returned invalid string format: " . $result);
}

function formatDate(int $year, int $month, int $day, string $format): string
{
    $map = [
        "Y" => str_pad((string) $year, 4, "0", STR_PAD_LEFT),
        "m" => str_pad((string) $month, 2, "0", STR_PAD_LEFT),
        "d" => str_pad((string) $day, 2, "0", STR_PAD_LEFT),
    ];

    return strtr($format, $map);
}

function detectDelimiter(string $text): string
{
    if ($text === "") {
        return "";
    }
    
    if (preg_match("/[^Ymd0-9]/", $text, $matches)) {
        return $matches[0];
    }

    return "";
}

function formatRequiresDay(string $format): bool
{
    return strpos($format, "d") !== false || strpos($format, "D") !== false;
}

function checkFormatHasDay(string $format): bool
{
    return strpos($format, "d") !== false || strpos($format, "j") !== false;
}

function execute(
    string $toCalendar = "gregorian",
    string $formatOutput = "Y-m-d",
    ?string $date = null,
    string $fromCalendar = "gregorian",
    ?string $formatInput = null
): string {
    try {
        $parsedDate = parseInputDate($date, $formatInput);
        
        $sourceYear = $parsedDate["year"];
        $sourceMonth = $parsedDate["month"];
        $sourceDay = $parsedDate["day"];
        $inputHasDay = $parsedDate["has_day"];

        if (strtolower($fromCalendar) === "gregorian") {
            $gregorianYear = $sourceYear;
            $gregorianMonth = $sourceMonth;
            $gregorianDay = $sourceDay;
        } else {
            $delimiter = detectDelimiter($formatInput ?? $date ?? "");
            $fromConverter = CalendarConverterFactory::make($fromCalendar);
            $gregorianRaw = $fromConverter->toGregorian($sourceYear, $sourceMonth, $sourceDay, $delimiter);
            
            [$gregorianYear, $gregorianMonth, $gregorianDay] = extractDateTuple($gregorianRaw);
        }

        $toConverter = CalendarConverterFactory::make($toCalendar);
        $targetRaw = $toConverter->fromGregorian($gregorianYear, $gregorianMonth, $gregorianDay, detectDelimiter($formatOutput));
        
        [$targetYear, $targetMonth, $targetDay] = extractDateTuple($targetRaw);

        if (!$inputHasDay && formatRequiresDay($formatOutput)) {
            $targetDay = 1;
        }

        return formatDate($targetYear, $targetMonth, $targetDay, $formatOutput);
    } catch (InvalidArgumentException $e) {
        throw new InvalidArgumentException("Error converting date: " . $e->getMessage());
    }
}

try {
    $date = getenv("MULTI_CALENDAR_DATE");
    $fromCalendar = getenv("MULTI_CALENDAR_FROM_CALENDAR");
    $toCalendar = getenv("MULTI_CALENDAR_TO_CALENDAR");
    $formatInput = getenv("MULTI_CALENDAR_FORMAT_INPUT");
    $formatOutput = getenv("MULTI_CALENDAR_FORMAT_OUTPUT");

    $result = execute($toCalendar, $formatOutput, $date, $fromCalendar, $formatInput);

    echo $result . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
'
