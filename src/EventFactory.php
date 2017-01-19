<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Calendar\Event;

class EventFactory
{
    protected $eventIntegration;

    /**
     * Setup a new event factory
     *
     * @param EventInterface $eventIntegration
     */
    public function __construct(EventInterface $eventIntegration)
    {
        $this->eventIntegration = $eventIntegration;
    }

    /**
     * create a new calendar instance
     *
     * @param \DateTimeZone $Timezone
     * @return Event $Event
     */
    public function load(\DateTimeZone $Timezone)
    {
        $Event = new Event(
            $this->eventIntegration,
            $Timezone
        );

        return $Event;
    }
}
