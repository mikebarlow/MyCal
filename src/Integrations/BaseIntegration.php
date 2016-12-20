<?php
namespace Snscripts\MyCal\Integrations;

use Snscripts\MyCal\Calendar\Calendar;

class BaseIntegration
{
    /**
     * extract the id field from the calendar
     *
     * @param Snscripts\MyCal\Calendar\Calendar
     * @return int|null $id
     */
    public function extractId(Calendar $Calendar)
    {
        $id = $Calendar->id;

        if (empty($id)) {
            return null;
        }

        return $id;
    }

    /**
     * extract the name field from the calendar
     *
     * @param Snscripts\MyCal\Calendar\Calendar
     * @return string $calName
     * @throws \DomainException
     */
    public function extractName(Calendar $Calendar)
    {
        $name = $Calendar->name;

        if (empty($name)) {
            throw new \DomainException('No Calendar ' . $name . ' set');
        }

        return $name;
    }

    /**
     * extract the id field from the calendar
     *
     * @param Snscripts\MyCal\Calendar\Calendar
     * @return int|null $id
     */
    public function extractUserId(Calendar $Calendar)
    {
        $user_id = $Calendar->user_id;

        if (empty($user_id)) {
            return null;
        }

        return $user_id;
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
        unset($data['id'], $data['name'], $data['user_id']);
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
