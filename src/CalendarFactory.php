<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Calendar\Calendar;

class CalendarFactory
{
    protected $calendarProvider;

    /**
     * Setup a new calendar factory with these providers
     *
     * @param CalendarInterface $calendarProvider
     */
    public function __construct(
        CalendarInterface $calendarProvider
    ) {
        $this->calendarProvider = $calendarProvider;
    }

    /**
     * create a new calendar instance
     *
     * @return Calendar $Calendar
     */
    public function newInstance()
    {
        return new Calendar(
            $this->calendarProvider
        );
    }
}
