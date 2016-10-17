<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Calendar\Date;
use DateTimeZone;

class DateFactory
{
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
