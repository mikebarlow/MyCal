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

    /**
     * given the content of the table wrap in a table
     *
     * @param string $content Table header and body to display
     * @return string
     */
    public function getTableWrapper($content)
    {
        return '<table class="' . $this->Options->displayTable['tableClass'] . '" id="' . $this->Options->displayTable['tableId'] . '">' .
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

        $day = $startOn = $this->Options->weekStartsOn;
        $first = true;

        foreach ($dates as $Date) {
            if ($first && ! $Date->isWeekStart()) {
                $dateDay = $Date->display('w');

                while($day != $dateDay) {
                    $body .= '<td class="' . $this->Options->displayTable['emptyClass'] . '"> &nbsp; </td>';

                    if ($day == 6) {
                        $day = 0;
                    } else {
                        $day++;
                    }
                }

                $first = false;
            } elseif ($Date->isWeekStart()) {
                $body .= '</tr><tr class="' . $this->Options->displayTable['rowClass'] . '">';
            }

            $body .= '<td class="' . $this->Options->displayTable['dateClass'] . '">' .
                $Date->display('j') .
                '</td>';
        }

        // work out if we need to pad the row
        $day = $dates->last()->display('w');
        if ($day == 6) {
            $day = 0;
        } else {
            $day++;
        }

        while($day !== $startOn) {
            $body .= '<td class="' . $this->Options->displayTable['emptyClass'] . '"> &nbsp; </td>';

            if ($day == 6) {
                $day = 0;
            } else {
                $day++;
            }
        }

        return $body . '</tr></tbody>';
    }
}
