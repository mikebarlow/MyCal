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
     * constructor to setup Options
     *
     * @param array $options calendar options
     */
    public function __construct($options)
    {
        $options = array_merge(
            $this->defaultOptions(),
            $options
        );

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
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
}
