<?php
namespace Snscripts\MyCal\Integrations\Null;

use Snscripts\Result\Result;
use Snscripts\MyCal\Calendar\Event as EventObj;
use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Integrations\BaseIntegration;

class Event extends BaseIntegration implements EventInterface
{
    /**
     * Save an event
     *
     * @param Snscripts\MyCal\Calendar\Event $Event
     * @return Snscripts\Result\Result $Result
     */
    public function save(EventObj $Event)
    {
        return Result::fail()
            ->setCode(Result::NOT_FOUND)
            ->setMessage('Null integration used, no database interactions available.');
    }

    /**
     * load the event
     *
     * @param int $id
     * @return Snscripts\Result\Result $Result
     */
    public function load($id)
    {
        return Result::fail()
            ->setCode(Result::NOT_FOUND)
            ->setMessage('Null integration used, no database interactions available.');
    }
}
