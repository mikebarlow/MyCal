---
layout: page
title: Saving Events
permalink: /saving-events
order: 80
---
# Saving Events

To create and save an event, you first need an instance of the Event object, this can be retrieved two ways.

    $Calendar = $CalendarFactory->load();

    // New event object - method one
    $Event = $Calendar->newEvent();

    // New event object - method two
    $Dates = $Calendar->dates('2017-01-01', '2017-01-31')->get();
    $Date = $Dates->get('2017-01-10');

    $Event = $Date->newEvent();


Method one will return a completely blank Event object whereas method two will return an Event object with the start date set to the date from the Date object that started the event (2017-01-10 in the example above).

Fluent methods are available for setting the start and end dates / times for the events. These automatically handle date time conversions as well so that the values stored on the object are always UTC, they are then converted to the defined timezone in the calendar options.

    $Event
        ->startsOn('YYYY-MM-DD')
        ->startsAt('HH:MM')
        ->endsOn('YYYY-MM-DD')
        ->endsAt('HH:MM');

When the save method is called, if the end time is before the start time an `\UnexpectedValueException` is thrown.

Aside from the start / end date and time, `name` is the only other required parameter on an Event object.

    $Event->name = 'My Event';

Other data can be created and associated with the event much like you can with a Calendar.

    $Event->location = 'some place, some city';
    $Event->price = [
        'adults' => '£20',
        'children' => '£10'
    ];

Once all the data is set, simply call the `save()` method on the Event.

    $Result = $Event->save();

`$Result` will contain the result object describing whether or not the event was saved. For documentation on the result object used see [https://github.com/mikebarlow/result](https://github.com/mikebarlow/result).

Once saved, should you wish to save it in a session or in some other database table for your project you can extract the Event ID number like so:

    $id = $Event->id;
