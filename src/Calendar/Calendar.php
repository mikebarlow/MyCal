<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\DateFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Traits;

class Calendar
{
    use Traits\Accessible;

    protected $calendarIntegration;
    protected $dateFactory;
    protected $Options;
    protected $getEvents = false;

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
     * display html table calendar of given dates
     *
     * @param string $start Start date to get Y-m-d format
     * @param string $end End date to get Y-m-d format
     * @return string
     */
    public function display($start, $end)
    {
        $dates = $this->build($start, $end);

        $header = $this->getTableHeader();
        $content = $this->getTableBody(
            $dates
        );

        return $this->getTableWrapper($header . $content);
    }

    /**
     * Get a collection of dates inclusive of given dates
     *
     * @todo tidy up events section
     * @param string $start Start date to get Y-m-d format
     * @param string $end End date to get Y-m-d format
     * @return \Cartalyst\Collections\Collection
     */
    public function build($start, $end)
    {
        $dates = $this->processDateRange(
            $this->getRange($start, $end)
        );

        $dateCollection = new \Cartalyst\Collections\Collection($dates);

        if ($this->withEvents) {
            $this->withEvents = false;

            $Event = $this->newEvent();
            $events = $Event->loadRange(
                $start,
                $end
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
     * given the content of the table wrap in a table
     *
     * @param string $content Table header and body to display
     * @return string
     */
    public function getTableWrapper($content)
    {
        return '<table class="' . $this->Options->displayTable['tableClass']
            . '" id="' . $this->Options->displayTable['tableId'] . '">' .
            $content .
            '</table>';
    }

    /**
     * build the days header for the table
     *
     * @return string
     */
    public function getTableHeader()
    {
        $header = '<thead><tr class="' . $this->Options->displayTable['headerRowClass'] . '">';

        $day = $this->Options->weekStartsOn;

        for ($i = 0; $i <= 6; $i++) {
            $header .= '<td class="' . $this->Options->displayTable['headerClass'] . '">' .
                $this->Options->days[$day] .
                '</td>';

            if ($day == 6) {
                $day = 0;
            } else {
                $day++;
            }
        }

        $header .= '</tr></thead>';
        return $header;
    }

    /**
     * generate the calendar table dates
     *
     * @todo Refactor this majorly, messy and got to be a better way to do it
     * @param \Cartalyst\Collections\Collection $dates Collection of dates to display
     * @return string
     */
    public function getTableBody($dates)
    {
        $body = '<tbody><tr class="' . $this->Options->displayTable['rowClass'] . '">';

        $day = $startOn = intval($this->Options->weekStartsOn);
        $first = true;

        foreach ($dates as $Date) {
            if ($first && ! $Date->isWeekStart()) {
                $dateDay = $Date->display('w');

                while ($day != $dateDay) {
                    $body .= '<td class="' . $this->Options->displayTable['emptyClass'] . '"> &nbsp; </td>';

                    if ($day == 6) {
                        $day = 0;
                    } else {
                        $day++;
                    }
                }
            } elseif (!$first && $Date->isWeekStart()) {
                $body .= '</tr><tr class="' . $this->Options->displayTable['rowClass'] . '">';
            }

            $body .= '<td class="' . $this->Options->displayTable['dateClass'] . '">' .
                $Date->display('j') .
                '</td>';
            $first = false;
        }

        // work out if we need to pad the row
        $day = $dates->last()->display('w');
        if ($day == 6) {
            $day = 0;
        } else {
            $day++;
        }

        while ($day !== $startOn) {
            $body .= '<td class="' . $this->Options->displayTable['emptyClass'] . '"> &nbsp; </td>';

            if ($day == 6) {
                $day = 0;
            } else {
                $day++;
            }
        }

        return $body . '</tr></tbody>';
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
