<?php
namespace Snscripts\MyCal\Integrations\Null;

use Snscripts\Result\Result;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Integrations\BaseIntegration;
use Snscripts\MyCal\Calendar\Calendar as CalendarObj;

class Calendar extends BaseIntegration implements CalendarInterface
{
    /**
     * Save a calendar and it's options
     *
     * @param Snscripts\MyCal\Calendar $Calendar
     * @return Snscripts\Result\Result $Result
     */
    public function save(CalendarObj $Calendar)
    {
        return Result::fail()
            ->setCode(Result::ERROR)
            ->setMessage('Null integration used, no database interactions available.');
    }

    /**
     * load the calendar and options
     *
     * @param int $id
     * @return Snscripts\Result\Result $Result
     */
    public function load($id)
    {
        return Result::fail()
            ->setCode(Result::NOT_FOUND)
            ->setMessage('Null integration used, no database interactions available.');
    }
}
