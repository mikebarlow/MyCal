---
layout: page
title: Saving Calendars
permalink: /saving-calendars
order: 40
---
# Saving Calendars

To get started, initiate the Calendar factory along with the Date and Events factories and your chosen integration. Currently only Laravel Eloquent integration is available for database usage and a Null integration for a manual calendar with no saving / loading from a database required.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Eloquent\Calendar,
        new \Snscripts\MyCal\DateFactory
    );

To save a calendar and it's options, initiate the Calendar Factory as above and call the `load` method:

    $Calendar = $CalendarFactory->load();

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
