<?php
namespace Snscripts\MyCal;

use DateTimeZone;
use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Calendar\Date;

class DateFactory
{
    protected $eventFactory;

    /**
     * Setup a new date factory
     *
     * @param EventFactory $eventFactory
     */
    public function __construct(EventFactory $eventFactory = null)
    {
        $this->eventFactory = $eventFactory;
    }

    /**
     * create a new date instance
     *
     * @param int $timestamp UTC timestamp
     * @param DateTimeZone $timezone
     * @param int $weekStart int corresponding to date of week - equivilant of 'w' format in php.net/date
     * @return Date $Date
     */
    public function newInstance($timestamp, DateTimeZone $Timezone, $weekStart)
    {
        return new Date(
            $timestamp,
            $Timezone,
            $weekStart,
            $this->eventFactory
        );
    }

    /**
     * return instance of the event factory
     *
     * @return Snscripts\MyCal\EventFactory|null
     */
    public function getEventFactory()
    {
        return $this->eventFactory;
    }
}
