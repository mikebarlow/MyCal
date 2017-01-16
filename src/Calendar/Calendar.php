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

            $dates[$date->format('Y-m-d')] = $this->dateFactory->newInstance(
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
