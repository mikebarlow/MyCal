---
layout: page
title: Calendars
permalink: /calendars
order: 40
---
# Calendars

To get started, initiate the Calendar factory along with the Date and Events factories and your chosen integration. Currently only Laravel Eloquent integration is available for database usage and a Null integration for a manual calendar with no saving / loading from a database required.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Eloquent\Calendar,
        new \Snscripts\MyCal\DateFactory(
            new \Snscripts\MyCal\EventFactory(
                new \Snscripts\MyCal\Integrations\Eloquent\Event
            )
        )
    );

## Saving

To save a calendar and it's options, initiate the Calendar Factory as above and call the `load` method:

    $Calendar = $CalendarFactory->load();

This will load a Calendar with the default options, to amend the default options, call the `Options` object when loading from the Calendar Factory

    $Calendar = $CalendarFactory->load(
        null, // Set to null when loading a new calendar
        \Snscripts\MyCal\Calendar\Options::set([
            'defaultTimezone' => 'America/New_York'
        ])
    );

When loading any defined options are merged with the default values so you only need to redefine the ones you wish to change. For the full list of options available and the default values, see [/MyCal/calendar-options](the calendar options page).

Once loaded, you can set a Calendar name (required data) and any other data you wish to store by simply defining them on the object.

    $Calendar->name = 'Mikes Calendar'; // Required data

    $Calendar->user_id = 1; // custom data for your project
    $Calendar->foo = 'bar'; // custom data for your project
    $Calendar->bar = 'foo'; // custom data for your project

To save your calendar simply call the `save` method on the Calendar.

    $Result = $Calendar->save();

`$Result` will contain the result object describing whether or not the calendar was saved. For documentation on the result object used see [https://github.com/mikebarlow/result](https://github.com/mikebarlow/result).

Once saved, should you wish to save it in a session or in some other database table for your project you can extract the Calendar ID number like so:

    $id = $Calendar->id;

## Loading

Loading a saved calendar has been made dead simple. When calling the `load` method on a Calendar Factory, simply set the Calendars ID number as the first parameter of the load method.

    $Calendar = $CalendarFactory->load(1); // Loading calendar with ID #1
    $Calendar = $CalendarFactory->load( // Loading calendar with ID #5
        5,
        \Snscripts\MyCal\Calendar\Options::set([
            'defaultTimezone' => 'America/New_York'
        ])
    );

In the example above where we are loading calendar #5 the options are only used as a fallback. This is because the options that were originally attached to a calendar are also saved.


