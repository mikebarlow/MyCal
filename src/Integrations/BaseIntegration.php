<?php
namespace Snscripts\MyCal\Integrations;

use Snscripts\MyCal\Calendar\Calendar;

class BaseIntegration
{
    protected $nameField = 'name';

    /**
     * extract the name field from the calendar DateFactory
     *
     * @param Snscripts\MyCal\Calendar\Calendar
     * @return string $calName
     * @throws \DomainException
     */
    public function extractName(Calendar $Calendar)
    {
        $name = $Calendar->name;

        if (empty($name)) {
            throw new \DomainException('No Calendar name set');
        }

        return $name;
    }

    /**
     * Extract the rest of the data into key => value with
     * any arrays / objects serialized
     *
     * @param Snscripts\MyCal\Calendar\Calendar
     * @return array
     */
    public function extractData(Calendar $Calendar)
    {
        $data = $Calendar->toArray();
        unset($data['name']);
        return $data;
    }

    /**
     * Extract the calendar options into key => value array
     *
     * @param Snscripts\MyCal\Calendar\Calendar
     * @return array
     */
    public function extractOptions(Calendar $Calendar)
    {
        return $Calendar->Options->toArray();
    }
}
