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

        $eventResults = $this->saveEvent($Event);
        if ($eventResults->isFail()) {
            return $eventResults;
        }

        $extrasResult = $this->saveExtras(
            $Event,
            $eventExtras
        );
        if ($extrasResult->isFail()) {
            return $extrasResult;
        }

        return Result::success()
            ->setCode(Result::SAVED)
            ->setExtra('event_id', $Event->id);
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
                'start_date',
                function ($Object) {
                    throw new \DomainException('No start date set on the event');
                }
            ),
            'end_date' => $this->extractVar(
                $Event,
                'end_date',
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
                ['id', 'name', 'calendar_id', 'start_date', 'end_date']
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

    /**
     * Save the event Model
     *
     * @param EventModel $Calendar
     * @return bool|string
     */
    public function saveEvent(EventModel $Event)
    {
        try {
            $Event->save();
        } catch (\Exception $e) {
            return Result::fail(
                Result::ERROR,
                $e->getMessage()
            );
        }

        return Result::success()
            ->setCode(Result::SAVED);
    }

    /**
     * Save the EventModel Model
     *
     * @param EventModel $Event
     * @param array $eventExtras
     * @return bool|string
     */
    public function saveExtras(EventModel $Event, $eventExtras)
    {
        try {
            $Event->eventExtra()->getRelated()
                ->where('event_id', '=', $Event->id)->delete();

            $Event->eventExtra()->saveMany($eventExtras);
        } catch (\Exception $e) {
            return Result::fail(
                Result::ERROR,
                $e->getMessage()
            );
        }

        return Result::success()
            ->setCode(Result::SAVED);
    }

    /**
     * load the event
     *
     * @param int $id
     * @return Snscripts\Result\Result $Result
     */
    public function load($id)
    {
        $eventData = $this->loadModel(new $this->eventModel, $id);

        if (empty($eventData)) {
            return Result::fail()
                ->setCode(Result::NOT_FOUND)
                ->setMessage('Could not load event #' . $id);
        }

        $eventData = $this->formatExtras($eventData);

        return Result::success()
            ->setCode(Result::FOUND)
            ->setExtra('eventData', $eventData);
    }

    /**
     * load the event model and data
     *
     * @param EventModel $EventModel
     * @param int $id
     * @return array
     */
    public function loadModel($EventModel, $id)
    {
        try {
            $EventModel = $EventModel
                ->where('id', '=', $id)
                ->with([
                    'eventExtra'
                ])
                ->first();
        } catch (\Exception $e) {
            return [];
        }

        return $EventModel->toArray();
    }

    /**
     * format the extra data
     *
     * @param array $eventData
     * @return array $formattedCalData
     */
    public function formatExtras($eventData)
    {
        $eventData['extras'] = [];
        foreach ($eventData['event_extra'] as $extras) {
            $eventData['extras'][$extras['slug']] = $extras['value'];
        }

        if (! empty($eventData['extras'])) {
            $eventData['extras'] = $this->unserializeData(
                $eventData['extras']
            );
        }
        unset($eventData['event_extra']);

        return $eventData;
    }

    /**
     * load a range of events given the dates
     *
     * @param string $startDate
     * @param string $endDate
     * @return Snscripts\Result\Result $Result
     */
    public function loadRange($startDate, $endDate)
    {
        $events = $this->getEventsByRange(
            new $this->eventModel,
            $startDate,
            $endDate
        );

        if (empty($events)) {
            return Result::fail()
                ->setCode(Result::NOT_FOUND)
                ->setMessage('Not events found');
        }

        $events = array_map(
            [$this, 'formatExtras'],
            $events
        );

        return Result::success()
            ->setCode(Result::FOUND)
            ->setExtra('events', $events);
    }

    public function getEventsByRange($EventModel, $startDate, $endDate)
    {
        $start = $startDate . ' 00:00:00';
        $end = $endDate . ' 23:59:59';

        try {
            $EventModel = $EventModel
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '>=', $startDate . ' 00:00:00')
                        ->where('end_date', '<=', $endDate . ' 23:59:59');
                })
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '>=', $startDate . ' 00:00:00')
                        ->where('start_date', '<=', $endDate . ' 23:59:59');
                })
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('end_date', '>=', $startDate . ' 00:00:00')
                        ->where('end_date', '<=', $endDate . ' 23:59:59');
                })
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate . ' 00:00:00')
                        ->where('end_date', '>=', $endDate . ' 23:59:59');
                })
                ->with([
                    'eventExtra'
                ])
                ->get();
        } catch (\Exception $e) {
            return [];
        }

        return $EventModel->toArray();
    }
}
