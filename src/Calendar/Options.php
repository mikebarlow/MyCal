<?php
namespace Snscripts\MyCal\Calendar;

class Options
{
    /**
     * Week starts on
     */
    public $weekStartsOn;

    /**
     * Default timezone
     *
     * @see http://php.net/manual/en/timezones.php
     */
    public $defaultTimezone;

    /**
     * Static method for loading options up
     *
     * @static
     * @param array $userOpts
     * @return Options $Options
     */
    public static function set($userOpts = [])
    {
        $Options = new static;
        $Options->mergeVars(
            $Options->defaultOptions(),
            $userOpts
        );

        return $Options;
    }

    /**
     * default calendar Options
     *
     * @return array $options
     */
    public function defaultOptions()
    {
        return [
            'weekStartsOn' => Date::MONDAY,
            'defaultTimezone' => 'Europe/London'
        ];
    }

    /**
     * Merge the default options with the user supplied
     *
     * @param array $defaults
     * @param array $userOpts
     */
    public function mergeVars($defaults, $userOpts)
    {
        $options = array_merge(
            $this->defaultOptions(),
            $userOpts
        );

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
