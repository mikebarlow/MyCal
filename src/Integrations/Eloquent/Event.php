<?php
namespace Snscripts\MyCal\Integrations\Eloquent;

use Snscripts\Result\Result;
use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Calendar\Event as EventObj;
use Snscripts\MyCal\Integrations\BaseIntegration;
use Snscripts\MyCal\Integrations\Eloquent\Models\Event as EventModel;
use Snscripts\MyCal\Integrations\Eloquent\Models\EventExtra as EventExtraModel;

class Event extends BaseIntegration implements EventInterface
{
    protected $eventModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\Event';
    protected $extraModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\EventExtra';

    /**
     * Save an event
     *
     * @param Snscripts\MyCal\Calendar\Event $Event
     * @return Snscripts\Result\Result $Result
     */
    public function save(EventObj $EventObj)
    {
        $data = $this->getEventData($EventObj);

        $Event = $this->setupModel(
            new $this->eventModel,
            $data
        );
        $eventExtras = $this->setupExtras(
            new $this->extraModel,
            $data
        );





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

    /**
     * setup new modal
     *
     * @param EventModel $Event
     * @param array $data array of modal data
     * @return EventModel
     */
    public function setupModel(EventModel $Event, $data)
    {
        if (! empty($data['id'])) {
            $Event = $Event->find($data['id']);
        } else {
            $Event->id = $data['id'];
        }

        $Event->name = $data['name'];
        $Event->start_date = $data['start_date'];
        $Event->end_date = $data['end_date'];
        $Event->calendar_id = $data['calendar_id'];

        return $Event;
    }

    /**
     * setup any model extras
     *
     * @param EventExtraModel $ExtraModel
     * @param array $data array of modal data
     * @return array $ExtraModel
     */
    public function setupExtras(EventExtraModel $ExtraModel, $data)
    {
        if (empty($data['extras'])) {
            return [];
        }

        return array_map(
            function ($value, $key) use ($ExtraModel) {
                $Extra = $ExtraModel->newInstance();
                $Extra->slug = $key;
                $Extra->value = $value;

                return $Extra;
            },
            $data['extras'],
            array_keys($data['extras'])
        );
    }

}
