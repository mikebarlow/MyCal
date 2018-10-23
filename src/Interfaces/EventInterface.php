<?php
namespace Snscripts\MyCal\Interfaces;

use Snscripts\MyCal\Calendar\Options;
use Snscripts\MyCal\Calendar\Event as EventObj;

interface EventInterface
{
    /**
     * Save an event
     *
     * @param Snscripts\MyCal\Calendar\Event $Event
     * @return Snscripts\Result\Result $Result
     */
    public function save(EventObj $Event);

    /**
     * load the event
     *
     * @param int $id
     * @return Snscripts\Result\Result $Result
     */
    public function load($id);

    /**
     * set the options object
     *
     * @param Options $options
     */
    public function setOptions(Options $options);
}
