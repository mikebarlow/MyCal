<?php

namespace Snscripts\MyCal\Integrations\Eloquent;

use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class MyCalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'Snscripts\MyCal\CalendarFactory',
            function ($app) {
                return new \Snscripts\MyCal\CalendarFactory(
                    new \Snscripts\MyCal\Integrations\Eloquent\Calendar,
                    new \Snscripts\MyCal\DateFactory(
                        new \Snscripts\MyCal\EventFactory(
                            new \Snscripts\MyCal\Inegrations\Eloquent\Event
                        )
                    )
                );
            }
        );
    }

    public function boot()
    {
        $this->loadMigrationsFrom(
            __DIR__.'/Migrations'
        );
    }
}
