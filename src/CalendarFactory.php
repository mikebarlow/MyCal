<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\DateFactory;
use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Calendar\Options;

class CalendarFactory
{
    protected $calendarIntegration;
    protected $dateFactory;
    protected $eventFactory;

    /**
     * Setup a new calendar factory
     *
     * @param CalendarInterface $calendarIntegration
     * @param DateFactory $dateFactory
     * @param EventFactory $eventFactory
     */
    public function __construct(
        CalendarInterface $calendarIntegration,
        DateFactory $dateFactory,
        EventFactory $eventFactory
    ) {
        $this->calendarIntegration = $calendarIntegration;
        $this->dateFactory = $dateFactory;
        $this->eventFactory = $eventFactory;
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

        $Calendar = new Calendar(
            $this->calendarIntegration,
            $this->dateFactory,
            $Options
        );

        if (! empty($id)) {
            $Calendar = $Calendar->load($id);
        }

        return $Calendar;
    }
}
