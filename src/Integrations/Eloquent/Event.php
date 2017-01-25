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

    /**
     * extract the event data for the model
     *
     * @todo review unixStart / unixEnd - for this to work they should be part of the Event::$data not set vars
     * @param Snscripts\MyCal\Event $Event
     * @return array $data array of modal data
     */
    public function getEventData(EventObj $Event)
    {
        return [
            'id' => $this->extractVar($Event, 'id'),
            'name' => $this->extractVar(
                $Event,
                'name',
                function ($Object) {
                    throw new \DomainException('No name set on the event');
                }
            ),
            'start_date' => $this->extractVar(
                $Event,
                'unixStart',
                function ($Object) {
                    throw new \DomainException('No start date set on the event');
                }
            ),
            'end_date' => $this->extractVar(
                $Event,
                'unixEnd',
                function ($Object) {
                    throw new \DomainException('No end date set on the event');
                }
            ),
            'calendar_id' => $this->extractVar(
                $Event,
                'calendar_id',
                function ($Object) {
                    throw new \DomainException('No calendar id set on the event');
                }
            ),
            'extras' => $this->extractData(
                $Event,
                ['id', 'name', 'calendar_id', 'unixStart', 'unixEnd']
            )
        ];
    }
}
