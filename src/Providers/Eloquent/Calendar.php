<?php
namespace Snscripts\MyCal\Providers\Eloquent;

use Snscripts\MyCal\Interfaces\CalendarInterface;
use Aura\Payload_Interface\PayloadStatus;

class Calendar implements CalendarInterface
{
    protected $model = 'Snscripts\Providers\Eloquent\Models\Calendar';

    protected $fields = [
        'id',
        'name',
        'is_active'
    ];

    /**
     * Save a calendar
     *
     * @param Calendar $Calendar
     * @return Aura\Payload_Interface\PayloadInterface $Payload
     */
    public function save($Calendar)
    {
        $Model = new $this->model;

        foreach ($this->fields as $field) {
            if (isset($Calendar->{$field})) {
                $Model->{$field} = $Calendar->{$field};
            }
        }

        // todo: validation?

        $PayloadFactory = new \Aura\Payload\PayloadFactory;
        $Payload = $PayloadFactory->newInstance();

        try {
            if ($Model->save()) {
                $Payload->setStatus(PayloadStatus::SUCCESS);
            } else {
                $Payload->setStatus(PayloadStatus::FAILURE);
            }
        } catch (\Exception $e) {
            $Payload->setStatus(PayloadStatus::ERROR)
                ->setMessages($e->getMessage())
                ->setInput($Model);
        }

        return $Payload;
    }
}
