<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\DateFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Calendar\Options;

class CalendarFactory
{
    protected $calendarProvider;
    protected $dateFactory;

    /**
     * Setup a new calendar factory with these providers
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

    /**
     * create a new calendar instance
     *
     * @param mixed $id Identifier to find calendar
     * @param Options $Options
     * @return Calendar $Calendar
     */
    public function load($id = '', Options $Options = null)
    {
        if (empty($options)) {
            $Options = Options::set();
        }

        return new Calendar(
            $this->calendarProvider,
            $this->dateFactory,
            $Options
        );
    }
}
