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
     * array of the start date and the time of the event in $this->Timezone time
     * @var array
     */
    protected $start = [
        'date' => '',
        'time' => '00:00:00'
    ];

    /**
     * array of the end date and the time of the event in $this->Timezone time
     * @var array
     */
    protected $end = [
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
     * Save the current calendar
     *
     * @return Snscripts\Result\Result
     */
    public function save()
    {
        if (is_a($this->Calendar, Calendar::class) && ! empty($this->Calendar->id)) {
            $this->calendar_id = $this->Calendar->id;
        }

        $Result = $this->eventIntegration->save($this);

        if ($Result->isSuccess()) {
            $id = $Result->getExtra('event_id');
            if (! empty($id)) {
                $this->id = $id;
            }
        }

        return $Result;
    }

    /**
     * load an event
     *
     * @param mixed $id
     * @return Event $this
     * @throws Snscripts\MyCal\Exceptions\NotFoundException
     */
    public function load($id)
    {
        $Result = $this->eventIntegration->load($id);

        if ($Result->isFail()) {
            throw new \Snscripts\MyCal\Exceptions\NotFoundException(
                $Result->getMessage()
            );
        }

        $eventData = $Result->getExtra('eventData');
        $extras = $eventData['extras'];
        unset($eventData['extras']);

        $this->setAllData(
            array_merge(
                $eventData,
                $extras
            )
        );

        return $this;
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
            $this->start_date,
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
            $this->end_date,
            $this->Timezone
        );
    }

    /**
     * covert the given timestamp into formatted date / time
     *
     * @param string $format The date format to use
     * @param string $date UTC date time to convert / display
     * @param \DateTimeZone $Timezone
     * @return string $dateTime
     */
    public function displayDate($format, $date, $Timezone)
    {
        $DateTime = new \DateTime(
            $date,
            new \DateTimeZone('UTC')
        );
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
        $this->start['date'] = $date;
        $this->start_date = $this->generateDateTime(
            $this->start,
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
        $this->start['time'] = $time;
        $this->start_date = $this->generateDateTime(
            $this->start,
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
        $this->end['date'] = $date;
        $this->end_date = $this->generateDateTime(
            $this->end,
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
        $this->end['time'] = $time;
        $this->end_date = $this->generateDateTime(
            $this->end,
            $this->Timezone
        );

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
        if (! $this->isStartBeforeEnd($this->start_date, $this->end_date)) {
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
     * generate the UTC date / time
     *
     * @param array $date array with 'date' element and 'time' element
     * @param \DateTImeZone $Timezone
     * @return string
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function generateDateTime($date, $Timezone)
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
        $DateTime->setTimezone(
            new \DateTimeZone('UTC')
        );

        return $DateTime->format('Y-m-d H:i:s');
    }

    /**
     * compare the start / end times
     * make sure we aren't time travelling
     *
     * @param string $utcStart start date time
     * @param string $utcEnd start date time
     * @return bool
     */
    public function isStartBeforeEnd($utcStart, $utcEnd)
    {
        if ($utcEnd === null) {
            return true;
        }

        return ($utcStart <= $utcEnd);
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

        $this->Timezone = new \DateTimeZone(
            $this->Calendar->getOptions()->defaultTimezone
        );
        return $this;
    }
}
