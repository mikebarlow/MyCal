<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\DateFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Traits;

class Calendar
{
    use Traits\Accessible;

    protected $calendarProvider;
    protected $dateFactory;

    /**
     * Setup a new calendar object
     *
     * @param CalendarInterface $calendarProvider
     * @param DateFactory $dateFactory
     */
    public function __construct(
        CalendarInterface $calendarProvider,
        DateFactory $dateFactory
    ) {
        $this->calendarProvider = $calendarProvider;
        $this->dateFactory = $dateFactory;
    }
}
