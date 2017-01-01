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
        //
    }

    public function boot()
    {
        $this->loadMigrationsFrom(
            __DIR__.'/Migrations'
        );
    }
}
