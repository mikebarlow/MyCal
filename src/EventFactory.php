<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Calendar\Event;
use Snscripts\MyCal\Calendar\Options;
use Snscripts\MyCal\Interfaces\EventInterface;

class EventFactory
{
    protected $eventIntegration;
    protected $options;

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
        $eventIntegration->setOptions($this->options);

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

    /**
     * set the options object
     *
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }
}
