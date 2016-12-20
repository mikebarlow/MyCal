<?php
namespace Snscripts\MyCal\Integrations\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    public function calendarExtra()
    {
        return $this->hasMany(
            'Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra',
            'calendar_id'
        );
    }

    public function option()
    {
        return $this->hasMany(
            'Snscripts\MyCal\Integrations\Eloquent\Models\Option',
            'calendar_id'
        );
    }
}
