<?php
namespace Snscripts\MyCal;

use Snscripts\MyCal\Calendar\Date;

class DateFactory
{
    /**
     * create a new date instance
     *
     * @return Date $Date
     */
    public function newInstance()
    {
        return new Date;
    }
}
