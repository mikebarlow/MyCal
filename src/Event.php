<?php
namespace Snscripts\MyCal;

class Event
{
    protected $eventProvider;

    /**
     * Setup a new Event Object
     *
     * @param EventInterface $eventProvider
     */
    public function __construct(EventInterface $eventProvider)
    {
        $this->$eventProvider = $eventProvider;
    }
}
