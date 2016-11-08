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
     * @todo tidy this up
     * @param date $start Start date to get Y-m-d format
     * @param date $end End date to get Y-m-d format
     * @return \Cartalyst\Collections\Collection
     */
    public function build($start, $end)
    {
        $dates = [];

        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $interval = new \DateInterval('P1D');
        $range = new \DatePeriod(
            $startDate,
            $interval,
            $endDate
        );

        $DateTimeZone = new DateTimeZone(
            $this->Options->defaultTimezone
        );

        foreach ($range as $date) {
            $dates[] = $this->dateFactory->newInstance(
                $date->getTimestamp(),
                $DateTimeZone,
                $this->Options->weekStart
            );
        }

        return new \Cartalyst\Collections\Collection($dates);
    }
}
