<?php
namespace Snscripts\MyCal\Integrations\Eloquent;

use Snscripts\Result\Result;
use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Integrations\BaseIntegration;
use Snscripts\MyCal\Calendar\Event as EventObj;

class Event extends BaseIntegration implements EventInterface
{
    protected $eventModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\Event';

    /**
     * Save an event
     *
     * @param Snscripts\MyCal\Calendar\Event $Event
     * @return Snscripts\Result\Result $Result
     */
    public function save(EventObj $Event)
    {
        return Result::success();
    }

    /**
     * load the event
     *
     * @param int $id
     * @return Snscripts\Result\Result $Result
     */
    public function load($id)
    {
        return Result::success();
    }
}
