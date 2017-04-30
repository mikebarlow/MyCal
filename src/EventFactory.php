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
     * create a new instance of the event object
     *
     * @param EventInterface $eventIntegration
     * @param \DateTimeZone $Timezone
     * @return Event $Event
     */
    public function newInstance(
        EventInterface $eventIntegration,
        \DateTimeZone $Timezone
    ) {
        return new Event(
            $eventIntegration,
            $Timezone
        );
    }

    /**
     * create a new event instance
     *
     * @param \DateTimeZone $Timezone
     * @param int $id Event id to load
     * @return Event $Event
     */
    public function load(\DateTimeZone $Timezone, $id = null)
    {
        $Event = $this->newInstance(
            $this->eventIntegration,
            $Timezone
        );

        if (! empty($id)) {
            $Event = $Event->load($id);
        }

        return $Event;
    }
}
