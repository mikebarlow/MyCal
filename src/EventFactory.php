<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Event;

class EventFactory
{
    protected $calendarProvider;
    protected $eventProvider;

    /**
     * Setup a new calendar factory with these providers
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
     * create a new event instance
     *
     * @return Event $Event
     */
    public function newInstance()
    {
        return new Event(
            $this->calendarProvider,
            $this->eventProvider
        );
    }
}
