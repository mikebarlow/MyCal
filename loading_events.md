---
layout: page
title: Loading Events
permalink: /loading-events
order: 90
---
# Loading Events

To load events for an entire date range, simply call the `withEvents()` method when loading a date collection:

    $Calendar = $CalendarFactory->load($calendarId);
    $dates = $Calendar
        ->withEvents()
        ->dates('2017-01-01', '2017-01-31')
        ->get(); // Returns date collection

If you have an Event id and wish to load just that specific event object you can do so via the Calendar Factory

    $Event = $CalendarFactory->loadEvent($eventId);

    echo $Event->name;

To load a new event there are two methods to do this:

    $Calendar = $CalendarFactory->load();

    // New event object - method one
    $Event = $Calendar->newEvent();

    // New event object - method two
    $Dates = $Calendar->dates('2017-01-01', '2017-01-31')->get();
    $Date = $Dates->get('2017-01-10');

    $Event = $Date->newEvent();


Method one will return a completely blank Event object whereas method two will return an Event object with the start date set to the date from the Date object that started the event (2017-01-10 in the example above).