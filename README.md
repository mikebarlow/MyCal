# MyCal

[![Author](http://img.shields.io/badge/author-@mikebarlow-red.svg?style=flat-square)](https://twitter.com/mikebarlow)
[![Docs](https://img.shields.io/badge/docs-mikebarlow.co.uk/MyCal-blue.svg?style=flat-square)](https://mikebarlow.co.uk/MyCal)
[![Latest Version](https://img.shields.io/github/release/mikebarlow/mycal.svg?style=flat-square)](https://github.com/mikebarlow/mycal/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/mikebarlow/mycal/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/mikebarlow/MyCal/master.svg?style=flat-square)](https://travis-ci.org/mikebarlow/MyCal)

## Introduction

MyCal is a PSR-2 compliant package used for generating calendars in an object orientated way. Built in are helpers for generating a HTML table for the requested dates.

**This package is still a work in progress.**

## Requirements

### Composer

MyCal requires the following:

* "php": ">=5.6.0"
* "cartalyst/collections": "1.1.*",
* "snscripts/result": "1.0.*"

And the following if you wish to run in dev mode and run tests.

* "phpunit/phpunit": "~5.7"
* "squizlabs/php_codesniffer": "~2.0"

MyCal suggests the following for database integrations.

* "illuminate/database": "Use Eloquent as the method for saving your calendars / events"

## Installation

### Composer

Simplest installation is via composer.

    composer require snscripts/mycal 0.*

or adding to your projects `composer.json` file.

    {
        "require": {
            "snscripts/mycal": "0.*"
        }
    }

### Setup

To initiate the package setup the Calendar Factory along with the Date Factory and the calendar integration you wish to use.

Currently only Laravel Eloquent integration is available, you can setup the Calendar Factory like so:

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Null\Calendar,
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

To load any saved events simply call `withEvents()` before the `build` or `display` methods.

    $dates = $Calendar->withEvents()->build($startDate, $endDate);
    echo $Calendar->withEvents()->display($startDate, $endDate);

### Date collection

Each item in the collection of dates is an instance of a MyCal Date object, these objects come with a few helper methods.

    $dates = $Calendar->build($startDate, $endDate);
    foreach ($dates as $Date) {

        var_dump($Date->isWeekend()); // true if date is a saturday or sunday.
        var_dump($Date->isWeekStart()); // true if date matches the week start option defined on the calendar

        echo $Date->display($format);
    }

### Events

Below are quick code examples of how to load a blank new Event object, populate, save and finally display the start / end times.

    $Event = $CalendarFactory->newEvent();

    $Event->name = 'My Awesome Event';
    $Event->startsOn('2017-04-30')
        ->startsAt('12:15')
        ->endsOn('2017-04-30')
        ->endsAt('21:00');

    $Event->save();

    $Event->displayStart('jS M Y H:i');
    $Event->displayEnd('jS M Y H:i');


## Full Documentation

Full documentation for MyCal can be found within the `gh-pages` branch of the repo [https://github.com/mikebarlow/MyCal/](https://github.com/mikebarlow/MyCal/) or alternatively at [http://mikebarlow.co.uk/MyCal](http://mikebarlow.co.uk/MyCal).

## Changelog

You can view the changelog [HERE](https://github.com/mikebarlow/mycal/blob/master/CHANGELOG.md)

## Contributing

Please see [CONTRIBUTING](https://github.com/mikebarlow/mycal/blob/master/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](https://github.com/mikebarlow/mycal/blob/master/LICENSE) for more information.
