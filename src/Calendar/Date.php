<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\Traits;
use DateTimeZone;

class Date
{
    use Traits\Accessible;

    protected $timestamp;
    protected $datetime;
    protected $timezone;
    protected $isWeekend;
    protected $isWeekStart;

    const
        MONDAY    = 1,
        TUESDAY   = 2,
        WEDNESDAY = 3,
        THURSDAY  = 4,
        FRIDAY    = 5,
        SATURDAY  = 6,
        SUNDAY    = 0;

    /**
     * setup the date object
     *
     * @param int $timestamp UTC timestamp
     * @param DateTimeZone $timezone
     * @param int $weekStart int corresponding to date of week - equivilant of 'w' format in php.net/date
     */
    public function __construct($timestamp, \DateTimeZone $Timezone, $weekStart)
    {
        $this->datetime = date('Y-m-d H:i:s', $timestamp);
        $this->isWeekend = $this->setWeekend($this->datetime, $Timezone);
        $this->isWeekStart = $this->setWeekStart($this->datetime, $Timezone, $weekStart);

        $this->timestamp = $timestamp;
        $this->timezone = $Timezone;
    }

    /**
     * is this date a weekend date (sat / sun)
     *
     * @return bool
     */
    public function isWeekend()
    {
        return $this->isWeekend;
    }

    /**
     * based on the calendars settings, is this date the week start
     *
     * @return bool
     */
    public function isWeekStart()
    {
        return $this->isWeekStart;
    }

    /**
     * take the timestamp and timezone, work out if it's a weekend date and set
     *
     * @param string $date Date time string
     * @param DateTimeZone $timezone
     * @return bool
     */
    public function setWeekend($date, DateTimeZone $Timezone)
    {
        $DateTime = new \DateTime(
            $date,
            new DateTimeZone('UTC')
        );
        $DateTime->setTimezone($Timezone);

        return in_array(intval($DateTime->format('w')), [6, 0]);
    }

    /**
     * based on the calendars options, is this date the week start
     *
     * @param string $date Date time string
     * @param DateTimeZone $timezone
     * @param int $weekStart int corresponding to date of week - equivilant of 'w' format in php.net/date
     * @return bool
     */
    public function setWeekStart($date, DateTimeZone $Timezone, $weekStart)
    {
        $DateTime = new \DateTime(
            $date,
            new DateTimeZone('UTC')
        );
        $DateTime->setTimezone($Timezone);

        return (intval($DateTime->format('w')) === $weekStart);
    }

    /**
     * return the date given the format
     *
     * @param string $format Date format as php.net/date
     * @param DateTimeZone $Timezone Optional custom timezone to set
     * @return string $date
     */
    public function display($format, $Timezone = '')
    {
        $DateTime = new \DateTime(
            $this->datetime,
            new DateTimeZone('UTC')
        );

        if (! empty($Timezone) && is_a($Timezone, 'DateTimeZone')) {
            $DateTime->setTimezone($Timezone);
        } else {
            $DateTime->setTimezone($this->timezone);
        }

        return $DateTime->format($format);
    }
}
