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
     * @param int $id Event id to load
     * @return Event $Event
     */
    public function load(\DateTimeZone $Timezone, $id = null)
    {
        $Event = new Event(
            $this->eventIntegration,
            $Timezone
        );

        if (! empty($id)) {
            $Event = $Event->load($id);
        }

        return $Event;
    }
}
