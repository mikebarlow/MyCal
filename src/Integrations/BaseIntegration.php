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
        $name = $Calendar->{$this->nameField};

        if (empty($name)) {
            throw new \DomainException('No Calendar ' . $this->nameField . ' set');
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
        return $this->serializeData($data);
    }

    /**
     * Extract the calendar options into key => value array
     *
     * @param Snscripts\MyCal\Calendar\Calendar
     * @return array
     */
    public function extractOptions(Calendar $Calendar)
    {
        return $this->serializeData(
            $Calendar->getOptions()->toArray()
        );
    }

    /**
     * loop array of data and serialize any arrays / objects
     *
     * @param array $data
     * @return array
     */
    public function serializeData($data)
    {
        array_walk(
            $data,
            function (&$value, $key) {
                if (is_object($value) || is_array($value)) {
                    $value = serialize($value);
                }
            }
        );

        return $data;
    }
}
