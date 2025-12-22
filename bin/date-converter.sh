#!/bin/bash

print_help() {
  echo ""
  echo "Usage:"
  echo "  $0 [date] [options]"
  echo ""
  echo "Options:"
  echo "   date                    Optional. If not provided, current date (now) will be used."
  echo "  -df, --date-format       Input calendar (default: gregorian)"
  echo "  -t,  --to                Output calendar (default: gregorian)"
  echo "  -fi, --format-input      Input date format (optional)"
  echo "  -fo, --format-output     Output date format (default: Y-m-d)"
  echo "   -h, --help               Show this help message"
  echo ""
  echo "Examples:"
  echo "  $0 \"2025-12-20\" --to jalali --format-output Y/m/d"
  echo "  $0 \"1403-10-01\" --date-format jalali --to gregorian --format-output Y-m-d"
  echo "  $0 \"4723-01-05\" --date-format chinese --to gregorian --format-output Y-m-d"
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

use JobMetric\MultiCalendar\Console\ConvertDateCommand;

try {
    $date = getenv("MULTI_CALENDAR_DATE");
    $fromCalendar = getenv("MULTI_CALENDAR_FROM_CALENDAR");
    $toCalendar = getenv("MULTI_CALENDAR_TO_CALENDAR");
    $formatInput = getenv("MULTI_CALENDAR_FORMAT_INPUT");
    $formatOutput = getenv("MULTI_CALENDAR_FORMAT_OUTPUT");

    $command = new ConvertDateCommand();

    // execute(toCalendar, formatOutput, date, fromCalendar, formatInput)
    $result = $command->execute($toCalendar, $formatOutput, $date, $fromCalendar, $formatInput);

    echo $result . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
'
