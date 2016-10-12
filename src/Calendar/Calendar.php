<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Traits;

class Calendar
{
    use Traits\Accessible;

    protected $calendarProvider;

    /**
     * Setup a new calendar object
     *
     * @param CalendarInterface $calendarProvider
     */
    public function __construct(
        CalendarInterface $calendarProvider
    ) {
        $this->calendarProvider = $calendarProvider;
    }
}
