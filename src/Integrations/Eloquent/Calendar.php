<?php
namespace Snscripts\MyCal\Integrations\Eloquent;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Integrations\BaseIntegration;
use Snscripts\MyCal\Calendar\Calendar as CalendarObj;
use Snscripts\Result\Result;

class Calendar extends BaseIntegration implements CalendarInterface
{
    protected $calModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\Calendar';
    protected $extraModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra';
    protected $optModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\Option';

    /**
     * Save a calendar and it's options
     *
     * @todo refactor for tests
     * @todo look into best way to handle calendar extras model
     * @param Snscripts\MyCal\Calendar $Calendar
     * @return Snscripts\Result\Result $Result
     */
    public function save(CalendarObj $Calendar)
    {
        $id = $this->extractId($Calendar);
        $name = $this->extractName($Calendar);
        $user_id = $this->extractUserId($Calendar);

        $calData = $this->extractData($Calendar);
        $options = $this->extractOptions($Calendar);

        $Model = new $this->calModel;

        if (! empty($id)) {
            $Model = $Model->find($id);
        } else {
            $Model->id = $id;
        }

        $Model->name = $name;
        $Model->user_id = $user_id;

        $calendarExtras = array_map(
            function ($value, $key) {
                $Extra = new $this->extraModel;
                $Extra->slug = $key;
                $Extra->value = $value;

                return $Extra;
            },
            $calData,
            array_keys($calData)
        );

        $calendarOptions = array_map(
            function ($value, $key) {
                $Option = new $this->optModel;
                $Option->slug = $key;
                $Option->value = $value;

                return $Option;
            },
            $options,
            array_keys($options)
        );

        try {
            $Model->save();
        } catch (\Exception $e) {
            return Result::fail(
                Result::ERROR,
                $e->getMessage()
            );
        }

        try {
            $ExtraModel = $this->extraModel;
            $ExtraModel::where('calendar_id', '=', $Model->id)->delete();

            $Model->calendarExtra()->saveMany($calendarExtras);
        } catch (\Exception $e) {
            return Result::fail(
                Result::ERROR,
                $e->getMessage()
            );
        }

        try {
            $OptModel = $this->optModel;
            $OptModel::where('calendar_id', '=', $Model->id)->delete();

            $Model->calendarOption()->saveMany($calendarOptions);
        } catch (\Exception $e) {
            return Result::fail(
                Result::ERROR,
                $e->getMessage()
            );
        }

        return Result::success();
    }

    /**
     * load the calendar and options
     *
     * @param int $id
     * @return Snscripts\Result\Result $Result
     */
    public function load($id)
    {
        $Model = new $this->calModel;
        $Model = $Model
            ->where('id', '=', $id)
            ->with([
                'calendarExtra',
                'calendarOption'
            ])
            ->first();

        $calData = $Model->toArray();
        $calData['extras'] = [];
        foreach ($calData['calendar_extra'] as $extras) {
            $calData['extras'][$extras['slug']] = $extras['value'];
        }

        if (! empty($calData['extras'])) {
            $calData['extras'] = $this->unserializeData(
                $calData['extras']
            );
        }

        $calData['options'] = [];
        foreach ($calData['calendar_option'] as $options) {
            $calData['options'][$options['slug']] = $options['value'];
        }

        if (! empty($calData['options'])) {
            $calData['options'] = $this->unserializeData(
                $calData['options']
            );
        }
        unset($calData['calendar_extra'], $calData['calendar_option']);

        return Result::success()
            ->setExtra('calData', $calData);
    }
}
