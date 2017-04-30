---
layout: page
title: Using Events
permalink: /using-events
order: 70
---
# Using Events

To use events within MyCal, the Event Factory and Event Integration needs to be loaded within the Date Factory when you start up MyCal.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Eloquent\Calendar,
        new \Snscripts\MyCal\DateFactory(
            new \Snscripts\MyCal\EventFactory(
                new \Snscripts\MyCal\Integrations\Eloquent\Event
            )
        )
    );

If you try to use any of the Event functionality without loading the Event Factory an `\UnexpectedValueException('No Event Factory was loaded.');` will be thrown.

To load any events for the dates you are loading simply call `withEvents()` method:

    $Calendar = $CalendarFactory->load($calendarId);
    $dates = $Calendar
        ->withEvents()
        ->build('2017-01-01', '2017-01-31'); // Returns date collection

    // loop Date Collection and then loop Event Collection
    foreach ($dates as $Date) {
        echo $Date->display('d/m/Y') . '<br>';

        echo '<strong>Events</strong><br>';
        foreach ($Date->events() as $Event) {
            echo $Event->name . '<br>';
            echo 'Start: ' . $Event->displayStart('jS M Y H:i') . '<br>';
            echo 'End: ' . $Event->displayEnd('jS M Y H:i') . '<br>';
            echo '---<br>';
        }

        echo '<hr>';
    }

MyCal supports events that span multiple days, in those instances an Event object will be created and assigned to each Date object that the Event takes place on.