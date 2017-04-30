<?php
namespace Snscripts\MyCal\Integrations;

use Snscripts\MyCal\Calendar\Calendar;

class BaseIntegration
{
    /**
     * generic extract data method
     * with custom default OptionsMock
     *
     * @param object $Object The item we wish to extract from
     * @param string $var The variable to extract
     * @param null|closure $default return value if empty - defaults to null|closure accepts $Object as a param
     * @return mixed $item
     */
    public function extractVar($Object, $var, $default = null)
    {
        $item = $Object->{$var};

        if (empty($item)) {
            if (is_callable($default)) {
                return $default($Object);
            }
            return $default;
        }

        return $item;
    }

    /**
     * Extract the rest of the data into key => value with
     * any arrays / objects serialized
     *
     * @param object $Object The item we wish to extract from
     * @param array $ignore Array of vars to ignore
     * @return array
     */
    public function extractData($Object, $ignore = [])
    {
        $data = $Object->toArray();
        array_walk(
            $ignore,
            function ($value, $key) use (&$data) {
                unset($data[$value]);
            }
        );

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

    /**
     * loop array of data and unserialize any arrays / objects
     *
     * @param array $data
     * @return array
     */
    public function unserializeData($data)
    {
        array_walk(
            $data,
            function (&$value, $key) {
                $unserialized = @unserialize($value);
                if ($unserialized !== false) {
                    $value = $unserialized;
                }
            }
        );

        return $data;
    }
}
