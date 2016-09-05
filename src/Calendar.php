<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\EventInterface;

class Calendar
{
    protected $calendarProvider;
    protected $eventProvider;

    /**
     * Setup a new calendar object
     *
     * @param CalendarInterface $calendarProvider
     * @param EventInterface $eventProvider
     */
    public function __construct(
        CalendarInterface $calendarProvider,
        EventInterface $eventProvider
    ) {
        $this->calendarProvider = $calendarProvider;
        $this->$eventProvider = $eventProvider;
    }
}
