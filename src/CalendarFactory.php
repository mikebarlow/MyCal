<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\DateFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Calendar\Options;

class CalendarFactory
{
    protected $calendarIntegration;
    protected $dateFactory;

    /**
     * Setup a new calendar factory
     *
     * @param CalendarInterface $calendarIntegration
     * @param DateFactory $dateFactory
     */
    public function __construct(
        CalendarInterface $calendarIntegration,
        DateFactory $dateFactory
    ) {
        $this->calendarIntegration = $calendarIntegration;
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
        if (empty($Options)) {
            $Options = Options::set();
        }

        return new Calendar(
            $this->calendarIntegration,
            $this->dateFactory,
            $Options
        );
    }
}
