<?php
namespace Snscripts\MyCal\Integrations\Eloquent;

use Snscripts\Result\Result;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Integrations\BaseIntegration;
use Snscripts\MyCal\Calendar\Calendar as CalendarObj;
use Snscripts\MyCal\Integrations\Eloquent\Models\Calendar as CalendarModel;
use Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra as CalendarExtraModel;
use Snscripts\MyCal\Integrations\Eloquent\Models\Option as OptionModel;

class Calendar extends BaseIntegration implements CalendarInterface
{
    protected $calModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\Calendar';
    protected $extraModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra';
    protected $optModel = 'Snscripts\MyCal\Integrations\Eloquent\Models\Option';

    /**
     * Save a calendar and it's options
     *
     * @todo look into best way to handle calendar extras model
     * @param Snscripts\MyCal\Calendar $Calendar
     * @return Snscripts\Result\Result $Result
     */
    public function save(CalendarObj $Calendar)
    {
        $data = $this->getCalendarData($Calendar);

        $Calendar = $this->setupModel(
            new $this->calModel,
            $data
        );
        $calendarExtras = $this->setupExtras(
            new $this->extraModel,
            $data
        );
        $calendarOptions = $this->setupOptions(
            new $this->optModel,
            $data
        );

        $calendarResult = $this->saveCalendar($Calendar);
        if ($calendarResult->isFail()) {
            return $calendarResult;
        }

        $extrasResult = $this->saveExtras(
            $Calendar,
            new $this->extraModel,
            $calendarExtras
        );
        if ($extrasResult->isFail()) {
            return $extrasResult;
        }

        $optionsResults = $this->saveOptions(
            $Calendar,
            new $this->optModel,
            $calendarOptions
        );
        if ($optionsResults->isFail()) {
            return $optionsResults;
        }

        return Result::success()
            ->setCode(Result::SAVED);
    }

    /**
     * extract the calendar data for the model
     *
     * @param Snscripts\MyCal\Calendar $Calendar
     * @return array $data array of modal data
     */
    public function getCalendarData(CalendarObj $Calendar)
    {
        return [
            'id' => $this->extractId($Calendar),
            'name' => $this->extractName($Calendar),
            'user_id' => $this->extractUserId($Calendar),
            'extras' => $this->extractData($Calendar),
            'options' => $this->extractOptions($Calendar)
        ];
    }

    /**
     * setup new modal
     *
     * @param CalendarModel $Calendar
     * @param array $data array of modal data
     * @return CalendarModel
     */
    public function setupModel(CalendarModel $Calendar, $data)
    {
        if (! empty($data['id'])) {
            $Calendar = $Calendar->find($data['id']);
        } else {
            $Calendar->id = $data['id'];
        }

        $Calendar->name = $data['name'];
        $Calendar->user_id = $data['user_id'];

        return $Calendar;
    }

    /**
     * setup any model extras
     *
     * @param CalendarExtraModel $ExtraModel
     * @param array $data array of modal data
     * @return array $calendarExtras
     */
    public function setupExtras(CalendarExtraModel $ExtraModel, $data)
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
     * setup any model extras
     *
     * @param CalendarExtraModel $Extra
     * @param array $data array of modal data
     * @return array $calendarExtras
     */
    public function setupOptions(OptionModel $OptionModel, $data)
    {
        if (empty($data['options'])) {
            return [];
        }

        return array_map(
            function ($value, $key) use ($OptionModel) {
                $Option = $OptionModel->newInstance();
                $Option->slug = $key;
                $Option->value = $value;

                return $Option;
            },
            $data['options'],
            array_keys($data['options'])
        );
    }

    /**
     * Save the calendar Model
     *
     * @param CalendarModel $Calendar
     * @return bool|string
     */
    public function saveCalendar(CalendarModel $Calendar)
    {
        try {
            $Calendar->save();
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
     * Save the calendarExtra Model
     *
     * @param CalendarModel $Calendar
     * @param CalendarExtraModel $CalendarExtra
     * @param array $extras
     * @return bool|string
     */
    public function saveExtras(
        CalendarModel $Calendar,
        CalendarExtraModel $ExtraModel,
        $calendarExtras
    ) {
        try {
            //$ExtraModel->where('calendar_id', '=', $Calendar->id)->delete();

            $Calendar->calendarExtra()->getRelated()
                ->where('calendar_id', '=', $Calendar->id)->delete();

            $Calendar->calendarExtra()->saveMany($calendarExtras);
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
     * Save the Options Model
     *
     * @param CalendarModel $Calendar
     * @param CalendarExtraModel $CalendarExtra
     * @param array $extras
     * @return bool|string
     */
    public function saveOptions(
        CalendarModel $Calendar,
        OptionModel $OptionModel,
        $calendarOptions
    ) {
        try {
            $OptionModel->where('calendar_id', '=', $Model->id)->delete();
            $Calendar->calendarOption()->saveMany($calendarOptions);
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
     * load the calendar and options
     *
     * @todo refactor for tests
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

        if (empty($Model)) {
            return Result::fail()
                ->setCode(Result::NOT_FOUND)
                ->setMessage('Could not load calendar #' . $id);
        }

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
            ->setCode(Result::FOUND)
            ->setExtra('calData', $calData);
    }
}
