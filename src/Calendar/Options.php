<?php
namespace Snscripts\MyCal\Calendar;

use Snscripts\MyCal\Traits;

class Options
{
    use Traits\Accessible;

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
            'defaultTimezone' => 'Europe/London',
            'days' => [
                0 => 'Sun',
                1 => 'Mon',
                2 => 'Tue',
                3 => 'Wed',
                4 => 'Thu',
                5 => 'Fri',
                6 => 'Sat'
            ]
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
            $defaults,
            $userOpts
        );

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
