---
layout: page
title: Loading Calendars
permalink: /loading-calendars
order: 50
---
# Loading Calendars

To get started, initiate the Calendar factory along with the Date and Events factories and your chosen integration. Currently only Laravel Eloquent integration is available for database usage and a Null integration for a manual calendar with no saving / loading from a database required.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Eloquent\Calendar,
        new \Snscripts\MyCal\DateFactory(
            new \Snscripts\MyCal\EventFactory(
                new \Snscripts\MyCal\Integrations\Eloquent\Event
            )
        )
    );

Loading a saved calendar has been made dead simple. When calling the `load` method on a Calendar Factory, simply set the Calendars ID number as the first parameter of the load method.

    $Calendar = $CalendarFactory->load(1); // Loading calendar with ID #1
    $Calendar = $CalendarFactory->load( // Loading calendar with ID #5
        5,
        \Snscripts\MyCal\Calendar\Options::set([
            'defaultTimezone' => 'America/New_York'
        ])
    );

In the example above where we are loading calendar #5 the options are only used as a fallback. This is because the options that were originally attached to a calendar are also saved.
