<?php
namespace Snscripts\MyCal\Integrations\Eloquent;

use Snscripts\Result\Result;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Integrations\BaseIntegration;
use Snscripts\MyCal\Calendar\Calendar as CalendarObj;
use Snscripts\MyCal\Integrations\Eloquent\Models\Option as OptionModel;
use Snscripts\MyCal\Integrations\Eloquent\Models\Calendar as CalendarModel;
use Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra as CalendarExtraModel;

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
    public function save(CalendarObj $CalendarObj)
    {
        $data = $this->getCalendarData($CalendarObj);

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
            $calendarExtras
        );
        if ($extrasResult->isFail()) {
            return $extrasResult;
        }

        $optionsResults = $this->saveOptions(
            $Calendar,
            $calendarOptions
        );
        if ($optionsResults->isFail()) {
            return $optionsResults;
        }

        return Result::success()
            ->setCode(Result::SAVED)
            ->setExtra('calendar_id', $Calendar->id);
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
            'id' => $this->extractVar($Calendar, 'id'),
            'name' => $this->extractVar(
                $Calendar,
                'name',
                function ($Object) {
                    throw new \DomainException('No name set on the calendar');
                }
            ),
            'user_id' => $this->extractVar($Calendar, 'user_id'),
            'extras' => $this->extractData(
                $Calendar,
                ['id', 'name', 'user_id']
            ),
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
     * @param array $extras
     * @return bool|string
     */
    public function saveExtras(CalendarModel $Calendar, $calendarExtras)
    {
        try {
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
    public function saveOptions(CalendarModel $Calendar, $calendarOptions)
    {
        try {
            $Calendar->calendarOption()->getRelated()
                ->where('calendar_id', '=', $Calendar->id)->delete();

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
        $calData = $this->loadModel(new $this->calModel, $id);

        if (empty($calData)) {
            return Result::fail()
                ->setCode(Result::NOT_FOUND)
                ->setMessage('Could not load calendar #' . $id);
        }

        $calData = $this->formatExtras($calData);
        $calData = $this->formatOptions($calData);

        return Result::success()
            ->setCode(Result::FOUND)
            ->setExtra('calData', $calData);
    }

    /**
     * load the calendar model and data
     *
     * @param CalendarModel $CalModel
     * @param int $id
     * @return array
     */
    public function loadModel($CalModel, $id)
    {
        try {
            $CalModel = $CalModel
                ->where('id', '=', $id)
                ->with([
                    'calendarExtra',
                    'calendarOption'
                ])
                ->first();
        } catch (\Exception $e) {
            return [];
        }

        return $CalModel->toArray();
    }

    /**
     * format the extra data
     *
     * @param array $calData
     * @return array $formattedCalData
     */
    public function formatExtras($calData)
    {
        $calData['extras'] = [];
        foreach ($calData['calendar_extra'] as $extras) {
            $calData['extras'][$extras['slug']] = $extras['value'];
        }

        if (! empty($calData['extras'])) {
            $calData['extras'] = $this->unserializeData(
                $calData['extras']
            );
        }
        unset($calData['calendar_extra']);

        return $calData;
    }

    /**
     * format the calendar options
     *
     * @param array $calData
     * @return array $formattedCalData
     */
    public function formatOptions($calData)
    {
        $calData['options'] = [];
        foreach ($calData['calendar_option'] as $options) {
            $calData['options'][$options['slug']] = $options['value'];
        }

        if (! empty($calData['options'])) {
            $calData['options'] = $this->unserializeData(
                $calData['options']
            );
        }
        unset($calData['calendar_option']);

        return $calData;
    }
}
