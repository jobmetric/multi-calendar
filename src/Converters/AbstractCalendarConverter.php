<?php

namespace JobMetric\MultiCalendar\Converters;

use JobMetric\MultiCalendar\DateConverter;
use JobMetric\MultiCalendar\Contracts\CalendarConverterInterface;

/**
 * Base class for all concrete calendar converters.
 * Inherits protected low-level conversion helpers from DateConverter.
 */
abstract class AbstractCalendarConverter extends DateConverter implements CalendarConverterInterface
{
    // Intentionally empty. Shared helpers are inherited from DateConverter.
}
