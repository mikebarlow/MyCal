<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Traits;

class Event
{
    use Traits\Accessible;

    protected $eventIntegration;

    /**
     * The timezone object
     * @var \DateTimeZone
     */
    protected $Timezone;

    /**
     * unix timestamp of the start date & time
     * @var int
     */
    protected $unixStart;

    /**
     * array of the start date and the time of the event in $this->Timezone time
     * @var array
     */
    protected $startDate = [
        'date' => '',
        'time' => '00:00:00'
    ];

    /**
     * unix timestamp of the end date & time
     * @var int
     */
    protected $unixEnd;

    /**
     * array of the end date and the time of the event in $this->Timezone time
     * @var array
     */
    protected $endDate = [
        'date' => '',
        'time' => '00:00:00'
    ];

    /**
     * Setup a new Event object
     *
     * @param EventIntegration $eventIntegration
     * @param string $timezone
     */
    public function __construct(EventIntegration $eventIntegration, $timezone)
    {
        $this->eventIntegration = $eventIntegration;
        $this->setTimezone($timezone);
    }

    /**
     * set the start date
     *
     * @param string $date date in YYYY-MM-DD format
     * @return object $this
     */
    public function startsOn($date)
    {
        $this->startDate['date'] = $date;
        $this->unixStart = $this->generateTimestamp(
            $this->startDate,
            $this->Timezone
        );

        return $this;
    }

    /**
     * set the start time
     *
     * @param string $time
     * @return object $this
     */
    public function startsAt($time)
    {
        $this->startDate['time'] = $time;
        $this->unixStart = $this->generateTimestamp(
            $this->startDate,
            $this->Timezone
        );

        return $this;
    }

    /**
     * set the end date
     *
     * @param string $date
     * @return object $this
     */
    public function endsOn($date)
    {
        $this->endDate['date'] = $date;
        $this->unixEnd = $this->generateTimestamp(
            $this->endDate,
            $this->Timezone
        );

        return $this;
    }

    /**
     * set the end time
     *
     * @param string $time
     * @return object $this
     */
    public function endsAt($time)
    {
        $this->endDate['time'] = $time;
        $this->unixEnd = $this->generateTimestamp(
            $this->endDate,
            $this->Timezone
        );

        return $this;
    }

    /**
     * Set the timezone in use
     *
     * @param string $timezone
     * @return Event $this
     */
    public function setTimezone($timezone)
    {
        $this->$Timezone = new \DateTimeZone($timezone);

        return $this;
    }

    /**
     * return the timezone object in use
     *
     * @return \DateTimeZone
     */
    public function getTimezone()
    {
        return $this->Timezone;
    }

    /**
     * given an array of date and time, and a Timezone
     * generate the unix timestamp of that timezone
     *
     * @param array $date array with 'date' element and 'time' element
     * @param \DateTImeZone $Timezone
     * @return int $unixTimestamp
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function generateTimestamp($date, $Timezone)
    {
        if (empty($date['date']) || empty($date['time'])) {
            throw new \BadMethodCallException('Event::generateTimestamp - Both date and time elements are required to generate the timestamp');
        }

        if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $date['date']) !== 1) {
            throw new \InvalidArgumentException('Event::generateTimestamp - The date element of the $date variable should be in the format YYYY-MM-DD');
        }

        if (preg_match('/([0-9]{2}):([0-9]{2}):([0-9]{2})/', $date['time']) !== 1) {
            throw new \InvalidArgumentException('Event::generateTimestamp - The time element of the $date variable should be in the format HH:MM:SS');
        }

        $DateTime = new \DateTime(
            $date['date'] . ' ' . $date['time'],
            $Timezone
        );

        return $DateTime->getTimestamp();
    }
}
