<?php
namespace Snscripts\MyCal\Integrations\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class Event extends Model
{
    public function eventExtra()
    {
        return $this->hasMany(
            'Snscripts\MyCal\Integrations\Eloquent\Models\EventExtra',
            'event_id'
        );
    }

    public function calendar()
    {
        return $this->belongsTo(
            'Snscripts\MyCal\Integrations\Eloquent\Models\Calendar',
            'calendar_id'
        );
    }

    public function taggable()
    {
        return $this->morphTo();
    }
}
