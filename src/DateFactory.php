<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Calendar\Date;
use DateTimeZone;

class DateFactory
{
    protected $eventFactory;

    /**
     * Setup a new date factory
     *
     * @param EventFactory $eventFactory
     */
    public function __construct(EventFactory $eventFactory)
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
            $weekStart
        );
    }
}
