<?php
namespace Snscripts\MyCal\Integrations\Eloquent;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Integrations\BaseIntegration;
use Snscripts\MyCal\Calendar\Calendar as CalendarObj;

class Calendar extends BaseIntegration implements CalendarInterface
{
    protected $model = 'Snscripts\MyCal\Integrations\Eloquent\Models\Calendar';

    /**
     * Save a calendar and it's options
     *
     * @param Snscripts\MyCal\Calendar $Calendar
     * @return Snscripts\Result\Result $Result
     */
    public function save(CalendarObj $Calendar)
    {
        $name = $this->extractName($Calendar);
        $data = $this->extractData($Calendar);
        $options = $this->extractOptions($Calendar);

        
    }
}
