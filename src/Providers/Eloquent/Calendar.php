<?php
namespace Snscripts\MyCal\Providers\Eloquent;

use Snscripts\MyCal\Interfaces\CalendarInterface;

class Calendar implements CalendarInterface
{
    protected $model = 'Snscripts\Providers\Eloquent\Models\Calendar';

    /**
     * Save a calendar
     *
     * @param Calendar $Calendar
     * @return Aura\Payload_Interface\PayloadInterface $Payload
     */
    public function save($Calendar)
    {
    }
}
