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
}
