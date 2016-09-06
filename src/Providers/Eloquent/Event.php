<?php
namespace Snscripts\MyCal\Providers\Eloquent;

use Snscripts\MyCal\Interfaces\EventInterface;

class Event implements EventInterface
{
    protected $model = 'Snscripts\Providers\Eloquent\Models\Event';

    /**
     * Save an event
     *
     * @param Snscripts\MyCal\Event $Event
     * @return Aura\Payload_Interface\PayloadInterface $Payload
     */
    public function save($Event)
    {
    }
}
