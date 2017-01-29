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

    /**
     * load an event - passes off to the event factory
     *
     * @param int $id Event ID
     * @return Snscripts\MyCal\Calendar\Event
     */
    public function loadEvent($id)
    {
        $EventFactory = $this->dateFactory->getEventFactory();

        if (empty($EventFactory)) {
            throw new \UnexpectedValueException('No Event Factory was loaded.');
        }

        $Event = $EventFactory->load(
            new \DateTimeZone('UTC'),
            $id
        );

        $Calendar = $this->load($Event->calendar_id);
        $Event->setCalendar($Calendar);

        return $Event;
    }
}
