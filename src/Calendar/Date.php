<?php
namespace Snscripts\MyCal\Calendar;

use DateTimeZone;
use Snscripts\GetSet\GetSet;
use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Calendar\Event;
use Snscripts\MyCal\Calendar\Calendar;

class Date
{
    use GetSet;

    protected $Calendar;
    protected $datetime;
    protected $EventFactory;
    protected $isWeekend;
    protected $isWeekStart;
    protected $timestamp;
    protected $Timezone;
    protected $events;

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
     * @param EventFactory $eventFactory
     */
    public function __construct(
        $timestamp,
        \DateTimeZone $Timezone,
        $weekStart,
        EventFactory $EventFactory = null
    ) {
        $this->datetime = date('Y-m-d H:i:s', $timestamp);
        $this->isWeekend = $this->setWeekend($this->datetime, $Timezone);
        $this->isWeekStart = $this->setWeekStart($this->datetime, $Timezone, $weekStart);

        $this->timestamp = $timestamp;
        $this->Timezone = $Timezone;

        if (is_a($EventFactory, EventFactory::class)) {
            $this->EventFactory = $EventFactory;
        }

        $this->events = new \Cartalyst\Collections\Collection([]);
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
        $weekStart = intval($weekStart);

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
            $DateTime->setTimezone($this->Timezone);
        }

        return $DateTime->format($format);
    }

    /**
     * return the events collection
     *
     * @return \Cartalyst\Collections\Collection
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * start a new event on this date
     *
     * @return Snscripts\MyCal\Calendar\Event
     */
    public function newEvent()
    {
        $Event = $this->EventFactory->load($this->Timezone);
        list($date, $time) = explode(' ', $this->datetime);
        $Event->startsOn($date);

        if (is_a($this->Calendar, Calendar::class)) {
            $Event->setCalendar($this->Calendar);
        }

        return $Event;
    }

    /**
     * manually attach a new event to the end of this date
     *
     * @param Snscripts\MyCal\Calendar\Event $Event
     * @return Date $this
     */
    public function attachEvent(Event $Event)
    {
        $this->events()->push($Event);
        return $this;
    }

    /**
     * Set the parent Calendar to the Date
     * required when using Events
     *
     * @param Snscripts\MyCal\Calendar\Calendar $Calendar
     * @return Date $this
     */
    public function setCalendar(Calendar $Calendar)
    {
        $this->Calendar = $Calendar;
        return $this;
    }
}
