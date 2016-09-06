<?php
namespace Snscripts\MyCal\Interfaces;

use Snscripts\MyCal\Calendar;
use Aura\Payload_Interface\PayloadInterface;

interface CalendarInterface
{
    /**
     * Save a calendar
     *
     * @param Calendar $Calendar
     * @return PayloadInterface $Payload
     */
    public function save($Calendar);

}
