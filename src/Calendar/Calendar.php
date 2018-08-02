<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\GetSet\GetSet;
use Snscripts\MyCal\DateFactory;
use Illuminate\Support\Collection;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\FormatterInterface;

class Calendar
{
    use GetSet;

    protected $calendarIntegration;
    protected $dateFactory;
    protected $Options;
    protected $getEvents = false;
    protected $dates = [];

    /**
     * Setup a new calendar object
     *
     * @param CalendarInterface $calendarIntegration
     * @param DateFactory $dateFactory
     * @param Options $Options
     */
    public function __construct(
        CalendarInterface $calendarIntegration,
        DateFactory $dateFactory,
        Options $Options
    ) {
        $this->calendarIntegration = $calendarIntegration;
        $this->dateFactory = $dateFactory;
        $this->Options = $Options;
    }

    /**
     * load a new event
     *
     * @return Snscripts\MyCal\Calendar\Event
     * @throws \UnexpectedValueException
     */
    public function newEvent()
    {
        $EventFactory = $this->dateFactory->getEventFactory();

        if (empty($EventFactory)) {
            throw new \UnexpectedValueException('No Event Factory was loaded.');
        }

        $Event = $EventFactory->load(
            new \DateTimeZone(
                $this->Options->defaultTimezone
            )
        );

        return $Event;
    }

    /**
     * Save the current calendar
     *
     * @return Snscripts\Result\Result
     */
    public function save()
    {
        $Result = $this->calendarIntegration->save($this);

        if ($Result->isSuccess()) {
            $id = $Result->getExtra('calendar_id');
            if (! empty($id)) {
                $this->id = $id;
            }
        }

        return $Result;
    }

    /**
     * load a calendar and it's options
     *
     * @param mixed $id
     * @return Calendar $this
     * @throws Snscripts\MyCal\Exceptions\NotFoundException
     */
    public function load($id)
    {
        $Result = $this->calendarIntegration->load($id);

        if ($Result->isFail()) {
            throw new \Snscripts\MyCal\Exceptions\NotFoundException(
                $Result->getMessage()
            );
        }

        $calData = $Result->getExtra('calData');
        $extras = $calData['extras'];
        $calOptions = $calData['options'];
        unset($calData['extras'], $calData['options']);

        $this->setAllData(
            array_merge(
                $calData,
                $extras
            )
        );

        $Options = new \Snscripts\MyCal\Calendar\Options;
        $Options->setAllData($calOptions);
        $this->setOptions($Options);
        unset($calData['options']);

        return $this;
    }

    /**
     * Get a collection of dates inclusive of given dates
     *
     * @return \Illuminate\Support\Collection
     * @throws \BadMethodCallException
     */
    public function get()
    {
        if (empty($this->dates['start']) || empty($this->dates['end'])) {
            throw new \BadMethodCallException('Start and end dates are required');
        }

        $dates = $this->processDateRange(
            $this->getRange($this->dates['start'], $this->dates['end'])
        );

        $dateCollection = collect($dates);

        if ($this->withEvents) {
            $this->withEvents = false;

            $Event = $this->newEvent();
            $events = $Event->loadRange(
                $this->dates['start'],
                $this->dates['end']
            );

            foreach ($events as $Event) {
                $start = $Event->displayStart('Y-m-d');
                $end = $Event->displayEnd('Y-m-d');
                $DatePeriod = $this->getRange(
                    $start,
                    $end
                );

                foreach ($DatePeriod as $EventDate) {
                    $eventDateFormat = $EventDate->format('Y-m-d');

                    if ($dateCollection->has($eventDateFormat)) {
                        $dateCollection->get($eventDateFormat)
                            ->events()
                            ->put(
                                $Event->displayStart('Y-m-d H:i'),
                                $Event
                            );
                    }
                }
            }

            // go through and sort the events on each date
            foreach ($dateCollection as $Date) {
                $Date->events()->sort(
                    function ($Event1, $Event2) {
                        $start1 = $Event1->displayStart('Y-m-d H:i');
                        $start2 = $Event2->displayStart('Y-m-d H:i');

                        if ($start1 == $start2) {
                            return 0;
                        }

                        return ($start1 < $start2) ? -1 : 1;
                    }
                );
            }
        }

        return $dateCollection;
    }

    /**
     * define whether or not to return events for the date range
     *
     * @return Calendar $this
     */
    public function withEvents()
    {
        $this->withEvents = true;
        return $this;
    }

    /**
     * set the start / end dates you want to get
     *
     * @param string $start Start date to get Y-m-d format
     * @param string $end End date to get Y-m-d format
     * @return Calendar $this
     */
    public function dates($start, $end)
    {
        $this->dates = [
            'start' => $start,
            'end' => $end
        ];

        return $this;
    }

    /**
     * get a DatePeriod array of dates
     *
     * @param string $start Start date to get Y-m-d format
     * @param string $end End date to get Y-m-d format
     * @return |DatePeriod
     */
    public function getRange($start, $end)
    {
        $calTime = new \DateTimeZone(
            $this->Options->defaultTimezone
        );

        return new \DatePeriod(
            new \DateTime(
                $start,
                $calTime
            ),
            new \DateInterval('P1D'),
            (new \DateTime(
                $end,
                $calTime
            ))->modify("+1 Day")
        );
    }

    /**
     * given a DatePeriod return an array of
     * MyCal Date Objects
     *
     * @param \DatePeriod $range
     * @return array
     */
    public function processDateRange($range)
    {
        $UTCTime = new \DateTimeZone('UTC');
        $DateTimeZone = new \DateTimeZone(
            $this->Options->defaultTimezone
        );

        $dates = [];
        foreach ($range as $date) {
            $date->setTimezone($UTCTime);

            $DateObj = $this->dateFactory->newInstance(
                $date->getTimestamp(),
                $DateTimeZone,
                $this->Options->weekStartsOn
            );
            $DateObj->setCalendar($this);
            $dates[$date->format('Y-m-d')] = $DateObj;
        }

        return $dates;
    }

    /**
     * given a formatter and the date collection
     * display a calendar table
     *
     * @param FormatterInterface $Formatter
     * @param \Illuminate\Support\Collection $Dates
     * @return string
     */
    public function display(FormatterInterface $Formatter, Collection $Dates)
    {
        $Formatter->setCalendar($this);

        // process the header
        $header = $this->getTableHeader($Formatter);
        $body = $this->getTableBody($Formatter, $Dates);

        return $this->getTableWrapper($Formatter, $header, $body);
    }


    /**
     * given the content of the table wrap in a table
     *
     * @param FormatterInterface $Formatter
     * @param string $header
     * @param string $, $body
     * @return string
     */
    public function getTableWrapper(FormatterInterface $Formatter, $header, $body)
    {
        return $Formatter->parseTable($header, $body);
    }

    /**
     * build the days header for the table
     *
     * @param FormatterInterface $Formatter
     * @return string
     */
    public function getTableHeader(FormatterInterface $Formatter)
    {
        $day = $this->getOptions()->weekStartsOn;

        $cells = '';
        for ($i = 0; $i <= 6; $i++) {
            $cells .= $Formatter->parseHeaderCell($day);

            if ($day == 6) {
                $day = 0;
            } else {
                $day++;
            }
        }

        return $Formatter->parseHeaderRow($cells);
    }

    /**
     * generate the calendar table dates
     *
     * @todo Refactor this majorly, messy and got to be a better way to do it
     * @param FormatterInterface $Formatter
     * @param \Illuminate\Support\Collection $dates Collection of dates to display
     * @return string
     */
    public function getTableBody(FormatterInterface $Formatter, $dates)
    {
        $day = $startOn = intval($this->getOptions()->weekStartsOn);
        $rows = '';
        $cells = '';

        // pad the start row
        $firstDay = $dates->first()->display('w');
        while ($day != $firstDay) {
            $cells .= $Formatter->parseEmptyCell();

            if ($day == 6) {
                $day = 0;
            } else {
                $day++;
            }
        }

        // process the date collection
        foreach ($dates as $Date) {
            if ($Date->isWeekStart()) {
                $rows .= $Formatter->parseDateRow($cells);
                $cells = '';
            }

            $events = '';
            if ($Date->events()->count() > 0) {
                foreach ($Date->events() as $Event) {
                    $events .= $Formatter->parseEvent($Event, $Date);
                }
            }

            $cells .= $Formatter->parseDateCell($Date, $events);
        }

        // pad the end row
        $day = $dates->last()->display('w');
        if ($day == 6) {
            $day = 0;
        } else {
            $day++;
        }

        while ($day != $startOn) {
            $cells .= $Formatter->parseEmptyCell();

            if ($day == 6) {
                $day = 0;
            } else {
                $day++;
            }
        }

        $rows .= $Formatter->parseDateRow($cells);

        return $rows;
    }

    /**
     * getter for Options
     *
     * @return Snscripts\MyCal\Calendar\Option
     */
    public function getOptions()
    {
        return $this->Options;
    }

    /**
     * setter for Options
     *
     * @param Snscripts\MyCal\Calendar\Option $Options
     */
    public function setOptions(Options $Options)
    {
        $this->Options = $Options;
        return $this;
    }
}
