<?php
namespace Snscripts\MyCal\Interfaces;

use Snscripts\MyCal\Calendar\Calendar as CalendarObj;

interface CalendarInterface
{
    /**
     * Save a calendar and it's options
     *
     * @param Snscripts\MyCal\Calendar $Calendar
     * @return Snscripts\Result\Result $Result
     */
    public function save(CalendarObj $Calendar);

    /**
     * load the calendar and options
     *
     * @param int $id
     * @return Snscripts\Result\Result $Result
     */
    public function load($id);
}
