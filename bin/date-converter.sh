#!/bin/bash

# Multi-Calendar Date Converter
# Converts current date to specified calendar format
#
# Usage: ./convert-date.sh [calendar] [format]
# Example: ./convert-date.sh jalali Y-m-d
# Example: ./convert-date.sh hijri Y/m/d

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PACKAGE_DIR="$(dirname "$SCRIPT_DIR")"

# Get parameters
CALENDAR="${1:-jalali}"
FORMAT="${2:-Y-m-d}"

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed or not in PATH" >&2
    exit 1
fi

# Find vendor/autoload.php - check package directory first, then root
AUTOLOAD_FILE=""
if [ -f "$PACKAGE_DIR/vendor/autoload.php" ]; then
    AUTOLOAD_FILE="$PACKAGE_DIR/vendor/autoload.php"
elif [ -f "$PACKAGE_DIR/../../vendor/autoload.php" ]; then
    AUTOLOAD_FILE="$PACKAGE_DIR/../../vendor/autoload.php"
else
    # Try to find root directory by looking for composer.json
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

# Execute PHP command directly
php -r "
require_once '$AUTOLOAD_FILE';

use JobMetric\MultiCalendar\Console\ConvertDateCommand;

try {
    \$calendar = '$CALENDAR';
    \$format = '$FORMAT';
    
    \$command = new ConvertDateCommand();
    \$result = \$command->execute(\$calendar, \$format);
    
    echo \$result . PHP_EOL;
    exit(0);
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"
