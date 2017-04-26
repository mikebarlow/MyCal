---
layout: page
title: Installation
permalink: /installation
order: 20
---
# Requirements

MyCal requires the following:

* "php": ">=5.6.0"
* "cartalyst/collections": "1.1.*",
* "snscripts/result": "1.0.*"

And the following if you wish to run in dev mode and run tests.

* "phpunit/phpunit": "~5.7"
* "squizlabs/php_codesniffer": "~2.0"

MyCal suggests the following for database integrations.

* "illuminate/database": "Use Eloquent as the method for saving your calendars / events"

# Installation

Simplest installation is via composer.

    composer require snscripts/mycal 0.*

or adding to your projects `composer.json` file.

    {
        "require": {
            "snscripts/mycal": "0.*"
        }
    }

## Laravel Eloquent

If you are using the Laravel Eloquent Integration then you will need add the MyCal Service Provider to your `config/app.php` file within the providers section.

    'providers' => [
        /*
         * Package Service Providers...
         */
        Snscripts\MyCal\Integrations\Eloquent\MyCalServiceProvider::class
    ]

Next the tables that hold the calendar and event data need to be created. Run the standard artisan migration command.

    php artisan migrate
