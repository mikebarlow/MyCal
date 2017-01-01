<?php
namespace Snscripts\MyCal\Integrations\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class Calendar extends Model
{
    public function calendarExtra()
    {
        return $this->hasMany(
            'Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra',
            'calendar_id'
        );
    }

    public function calendarOption()
    {
        return $this->hasMany(
            'Snscripts\MyCal\Integrations\Eloquent\Models\Option',
            'calendar_id'
        );
    }
}
