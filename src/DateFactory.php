<?php
namespace Snscripts\MyCal;

use DateTimeZone;
use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Calendar\Date;
use Snscripts\MyCal\Calendar\Options;

class DateFactory
{
    protected $eventFactory;
    protected $options;

    /**
     * Setup a new date factory
     *
     * @param EventFactory $eventFactory
     */
    public function __construct(EventFactory $eventFactory = null)
    {
        $this->eventFactory = $eventFactory;
    }

    /**
     * create a new date instance
     *
     * @param int $timestamp UTC timestamp
     * @param DateTimeZone $timezone
     * @param int $weekStart int corresponding to date of week - equivilant of 'w' format in php.net/date
     * @return Date $Date
     */
    public function newInstance($timestamp, DateTimeZone $Timezone, $weekStart)
    {
        return new Date(
            $timestamp,
            $Timezone,
            $weekStart,
            $this->eventFactory
        );
    }

    /**
     * return instance of the event factory
     *
     * @return Snscripts\MyCal\EventFactory|null
     */
    public function getEventFactory()
    {
        if ($this->options !== null) {
            $this->eventFactory->setOptions($this->options);
        }

        return $this->eventFactory;
    }

    /**
     * set the options object
     *
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }
}
