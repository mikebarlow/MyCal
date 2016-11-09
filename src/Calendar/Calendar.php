<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\DateFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Traits;

class Calendar
{
    use Traits\Accessible;

    protected $calendarProvider;
    protected $dateFactory;
    protected $Options;

    /**
     * Setup a new calendar object
     *
     * @param CalendarInterface $calendarProvider
     * @param DateFactory $dateFactory
     * @param Options $Options
     */
    public function __construct(
        CalendarInterface $calendarProvider,
        DateFactory $dateFactory,
        Options $Options
    ) {
        $this->calendarProvider = $calendarProvider;
        $this->dateFactory = $dateFactory;
        $this->Options = $Options;
    }

    /**
     * Get a collection of dates inclusive of given dates
     *
     * @param string $start Start date to get Y-m-d format
     * @param string $end End date to get Y-m-d format
     * @return \Cartalyst\Collections\Collection
     */
    public function build($start, $end)
    {
        $dates = $this->processDateRange(
            $this->getRange($start, $end)
        );

        return new \Cartalyst\Collections\Collection($dates);
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
        $utcTime = new \DateTimeZone('UTC');

        return new \DatePeriod(
            new \DateTime(
                $start,
                $utcTime
            ),
            new \DateInterval('P1D'),
            (new \DateTime(
                $end,
                $utcTime
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
        $DateTimeZone = new \DateTimeZone(
            $this->Options->defaultTimezone
        );

        $dates = [];
        foreach ($range as $date) {
            $dates[] = $this->dateFactory->newInstance(
                $date->getTimestamp(),
                $DateTimeZone,
                $this->Options->weekStartsOn
            );
        }

        return $dates;
    }
}
