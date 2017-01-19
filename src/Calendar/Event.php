<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Traits;
use DateTimeZone;

class Event
{
    use Traits\Accessible;

    /**
     * The timezone object
     * @var \DateTimeZone
     */
    public $Timezone;

    /**
     * DB Event integration object
     * @var \Snscripts\MyCal\Interfaces\EventInterface
     */
    protected $eventIntegration;

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
     * @param EventInterface $eventIntegration
     * @param \DateTimeZone $Timezone
     */
    public function __construct(
        EventInterface $eventIntegration,
        DateTimeZone $Timezone
    ) {
        $this->eventIntegration = $eventIntegration;
        $this->Timezone = $Timezone;
    }

    /**
     * return the formatted start date
     *
     * @param string $format The date format to use
     * @return string $dateTime
     */
    public function displayStart($format)
    {
        return $this->displayDate(
            $format,
            $this->unixStart,
            $this->Timezone
        );
    }

    /**
     * return the formatted end date
     *
     * @param string $format The date format to use
     * @return string $dateTime
     */
    public function displayEnd($format)
    {
        return $this->displayDate(
            $format,
            $this->unixEnd,
            $this->Timezone
        );
    }

    /**
     * covert the given timestamp into formatted date / time
     *
     * @param string $format The date format to use
     * @param int $timestamp Unix Timestamp
     * @param \DateTimeZone $Timezone
     * @return string $dateTime
     */
    public function displayDate($format, $timestamp, $Timezone)
    {
        $DateTime = new \DateTime(
            'now',
            new \DateTimeZone('UTC')
        );
        $DateTime->setTimestamp($timestamp);
        $DateTime->setTimezone($Timezone);

        return $DateTime->format($format);
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
     * @todo compare against start to make sure time travel
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
     * @todo compare against start to make sure time travel
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
