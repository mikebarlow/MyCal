<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\EventInterface;

class Calendar extends BaseObject
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
        $this->eventProvider = $eventProvider;
    }

    /**
     * Save the current Calendar
     *
     * @return Aura\Payload_Interface\PayloadInterface $Payload
     */
    public function save()
    {
        return $this->calendarProvider->save($this);
    }
}
