<?php
namespace Snscripts\MyCal\Integrations\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class EventExtra extends Model
{
    public $incrementing = false;

    public function event()
    {
        return $this->belongsTo(
            'Snscripts\MyCal\Integrations\Eloquent\Models\Event',
            'event_id'
        );
    }
}
