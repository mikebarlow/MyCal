<?php
namespace Snscripts\MyCal\Calendar;

use DateTimeZone;
use Snscripts\MyCal\Traits;
use Snscripts\MyCal\Interfaces\EventInterface;

class Event
{
    use Traits\Accessible;

    /**
     * Parent Calendar object, used when saving events
     * @var \Snscripts\MyCal\Calendar\Event
     */
    protected $Calendar;

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
     * repeat event, hold the config for when / how long etc..
     * @todo Complete repeatable events - On Hold
     * @var array|null
     */
    protected $repeat;

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
     * @codeCoverageIgnore
     * setup the event to be repeatable
     *
     * @todo Complete repeatable events - On Hold
     * @return Event $this
     */
    public function repeatable()
    {
        $this->repeatable = [
            'frequency' => '',
            'until' => ''
        ];

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * end date for the repeatable event
     *
     * @todo Complete repeatable events - On Hold
     * @param string $dateTime date time in YYYY-MM-DD HH:MM:SS format
     * @return Event $this
     * @throws \InvalidArgumentException
     */
    public function until($dateTime)
    {
        list($date, $time) = explode(' ', $dateTime);

        if (! $this->isValidDate($date) || ! $this->isValidTime($time)) {
            throw new \InvalidArgumentException(
                'Event::until - The date time passed should be in the format of
                "YYYY-MM-DD HH:MM:SS"'
            );
        }

        $this->repeatable['until'] = $dateTime;

        return $this;
    }

    /**
     * Start to prepare the event for saving or attaching
     * This checks to see if the event spans multiple days
     *
     * @todo review - can the logic here be improved?
     * @param string $start Start date in YYYY-MM-DD format
     * @param string $end End date in YYYY-MM-DD format
     * @return Cartalyst\Collections\Collection
     */
    public function prepareEvent($start, $end)
    {
        if (! $this->isStartBeforeEnd($this->unixStart, $this->unixEnd)) {
            throw new \UnexpectedValueException(
                'The event end date can not occur before event start date'
            );
        }

        $events = [];

        if ($start === $end) {
            $events[$this->startDate['date']] = $this;
        } else {
            $dates = new \DatePeriod(
                new \DateTime(
                    $start,
                    new \DateTimeZone('UTC')
                ),
                new \DateInterval('P1D'),
                (new \DateTime(
                    $end,
                    new \DateTimeZone('UTC')
                ))->modify("+1 Day")
            );

            foreach ($dates as $Date) {
                $curDate = $Date->format('Y-m-d');
                $Event = $this;

                if ($curDate === $start) {
                    $Event->endsOn($start)
                        ->endsAt('23:59:59');
                } elseif ($curDate != $end && $curDate != $start) {
                    $Event->startsOn($curDate)
                        ->startsAt('00:00:00')
                        ->endsOn($curDate)
                        ->endsAt('23:59:59');
                } elseif ($curDate === $end) {
                    $Event->startsOn($curDate)
                        ->startsAt('00:00:00');
                }

                $events[$curDate] = $Event;
            }
        }

        return new \Cartalyst\Collections\Collection($events);
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
            throw new \BadMethodCallException(
                'Event::generateTimestamp - Both date and time elements
                are required to generate the timestamp'
            );
        }

        if (! $this->isValidDate($date['date'])) {
            throw new \InvalidArgumentException(
                'Event::generateTimestamp - The date element of the $date
                variable should be in the format YYYY-MM-DD'
            );
        }

        if (! $this->isValidTime($date['time'])) {
            throw new \InvalidArgumentException(
                'Event::generateTimestamp - The time element of the $date
                variable should be in the format HH:MM:SS'
            );
        }

        $DateTime = new \DateTime(
            $date['date'] . ' ' . $date['time'],
            $Timezone
        );

        return $DateTime->getTimestamp();
    }

    /**
     * compare the start / end times
     * make sure we aren't time travelling
     *
     * @param int $unixStart start timestamp
     * @param int $unixEnd end timestamp
     * @return bool
     */
    public function isStartBeforeEnd($unixStart, $unixEnd)
    {
        if (! is_int($unixEnd)) {
            return true;
        }

        return ($unixStart <= $unixEnd);
    }

    /**
     * check for a valid date format
     *
     * @param string $date Dates should be in YYYY-MM-DD format
     * @return bool
     */
    public function isValidDate($date)
    {
        return (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $date) === 1);
    }

    /**
     * check for a valid time format
     *
     * @param string $time Times should be in HH:MM:SS format
     * @return bool
     */
    public function isValidTime($time)
    {
        return (preg_match('/([0-9]{2}):([0-9]{2}):([0-9]{2})/', $time) === 1);
    }

    /**
     * Set the parent Calendar to the Event
     * required when saving
     *
     * @param Snscripts\MyCal\Calendar\Calendar $Calendar
     * @return Event $this
     */
    public function setCalendar(Calendar $Calendar)
    {
        $this->Calendar = $Calendar;
        return $this;
    }
}
