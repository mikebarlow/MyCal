# MyCal

[![Author](http://img.shields.io/badge/author-@mikebarlow-red.svg?style=flat-square)](https://twitter.com/mikebarlow)
[![Source Code](http://img.shields.io/badge/source-mikebarlow/mycal-brightgreen.svg?style=flat-square)](https://github.com/mikebarlow/mycal)
[![Latest Version](https://img.shields.io/github/release/mikebarlow/mycal.svg?style=flat-square)](https://github.com/mikebarlow/mycal/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/mikebarlow/mycal/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/mikebarlow/mycal/master.svg?style=flat-square)](https://travis-ci.org/mikebarlow/mycal)

## Introduction

MyCal is a PSR-2 compliant package used for generating calendars and associated events easily. The calendar also comes with easy methods to generate a table based view for the calendar. Everything is also object orientated for easy use.

## Requirements

### Composer

MyCal requires the following:

* "php": ">=5.5.0"
* "cartalyst/collections": "1.1.*",
* "snscripts/result": "1.0.*"

And the following if you wish to run in dev mode and run tests.

* "phpunit/phpunit": "~4.0"
* "squizlabs/php_codesniffer": "~2.0"

MyCal suggests the following for database integrations.

* "illuminate/database": "Use Eloquent as the method for saving your calendars / events"

## Installation

### Composer

Simplest installation is via composer.

    composer require snscripts/mycal 1.*

or adding to your projects `composer.json` file.

    {
        "require": {
            "snscripts/mycal": "1.*"
        }
    }

### Setup

To initiate the package setup the Calendar Factory along with the Date Factory and the calendar integration you wish to use.

Currently only Laravel Eloquent integration is available, you can setup the Calendar Factory like so:

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Nullable\Calendar,
        new \Snscripts\MyCal\DateFactory
    );

## Usage

### Basics

Once setup as above, load up a new calendar by using the Calendar Factory load method

    $Calendar = $CalendarFactory->load();

This creates a new Calendar object with default options. You can then generate a collection of dates by calling

    $dates = $Calendar->build($startDate, $endDate);

Both start and end date should be in `YYYY-MM-DD` format. If you wish to generate a html table for the dates automatically you can call:

    echo $Calendar->display($startDate, $endDate);

### Date collection

Each item in the collection of dates is an instance of a MyCal Date object, these objects come with a few helper methods.

    $dates = $Calendar->build($startDate, $endDate);
    foreach ($dates as $Date) {

        var_dump($Date->isWeekend()); // true if date is a saturday or sunday.
        var_dump($Date->isWeekStart()); // true if date matches the week start option defined on the calendar

        echo $Date->display($format, $DateTimeZone = '');
        // $format is the date format to use as described on http://php.net/date
        // $DateTImeZone should be passed as a DateTimeZone object (http://php.net/datetimezone)
        // If omitted a DateTimeZone object is created from the timezone option defined on the calendar
    }

## Database Integrations

### Laravel / Eloquent

Out of the box MyCal comes with Eloquent / Laravel integration. If you are using Laravel 5.x you can simply include the MyCal Service Provider in your `config/app.php` file.

	'providers' => [
	    /*
	     * Laravel Framework Service Providers...
	     */
	
	    // ...
	
	    /*
	     * Application Service Providers...
	     */    
	     Snscripts\MyCal\Integrations\Eloquent\MyCalServiceProvider::class
	 ]

Then run `php artisan migrate` in your command line to run your applications migrations. The MyCal Service Provider makes MyCal migrations available to Laravel and will automatically setup the tables needed.

If you are just using Eloquent outside of Laravel you can still use the Eloquent integration however you will need to manually create the database tables.

    -- Create syntax for TABLE 'calendar_extras'
    CREATE TABLE `calendar_extras` (
      `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `value` text COLLATE utf8_unicode_ci NOT NULL,
      `calendar_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`slug`,`calendar_id`),
      KEY `calendar_extras_calendar_id_foreign` (`calendar_id`),
      CONSTRAINT `calendar_extras_calendar_id_foreign` FOREIGN KEY (`calendar_id`) REFERENCES `calendars` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- Create syntax for TABLE 'calendars'
    CREATE TABLE `calendars` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `user_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- Create syntax for TABLE 'options'
    CREATE TABLE `options` (
      `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `value` text COLLATE utf8_unicode_ci NOT NULL,
      `calendar_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`slug`,`calendar_id`),
      KEY `options_calendar_id_foreign` (`calendar_id`),
      CONSTRAINT `options_calendar_id_foreign` FOREIGN KEY (`calendar_id`) REFERENCES `calendars` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

Then when loading up the calendar factory make sure you use the Eloquent calendar integration.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Eloquent\Calendar,
        new \Snscripts\MyCal\DateFactory
    );

### Saving

Once setup with the integration, when you have loaded a blank calendar you can simply call the `save()` method to save the calendar along with the defined options and any extra data stored on the calendar object.

	$Calendar = $CalendarFactory->load();
	
	$Calendar->name = 'Mikes Calendar';
	$Calendar->foo = 'bar';
	
	$Result = $Calendar->save();
	
$Result will contain the result object describing whether or not the calendar was saved. For documentation on the result object used see [https://github.com/mikebarlow/result](https://github.com/mikebarlow/result). 

Once saved, should you wish to save it in a session or in some other database table you can extract the Calendar ID number like so:

	$id = $Calendar->id;	
	
### Loading

Assuming you have a calendar saved and have the ID number accessible, you can load up a calendar and all it's options by calling the load method on the Calendar Factory:

	$Calendar = $CalendarFactory->load($id);
	
	
	
