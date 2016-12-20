<?php
namespace Snscripts\MyCal\Integrations\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarExtra extends Model
{
    public $incrementing = false;

    public function calendar()
    {
        return $this->hasMany(
            'Snscripts\MyCal\Integrations\Eloquent\Models\Calendar',
            'calendar_id'
        );
    }
}
