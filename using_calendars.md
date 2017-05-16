---
layout: page
title: Using Calendars
permalink: /using-calendars
order: 30
---
# Using Calendars

To initiate the package, setup the Calendar Factory along with the Date Factory and the calendar integration you wish to use.

If no database integrations are necessary the Null integration can be used.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Null\Calendar,
        new \Snscripts\MyCal\DateFactory
    );

Once setup as above, load up a new calendar by using the Calendar Factory load method

    $Calendar = $CalendarFactory->load();

This will start a new Calendar with the default options, to amend the default options, call the `Options` object when loading from the Calendar Factory

    $Calendar = $CalendarFactory->load(
        null, // Set to null when loading a new calendar
        \Snscripts\MyCal\Calendar\Options::set([
            'defaultTimezone' => 'America/New_York'
        ])
    );

When loading, any defined options are merged with the default values so you only need to redefine the ones you wish to change. For the full list of options available and the default values, see [the calendar options page](/MyCal/calendar-options).

This creates a new Calendar object with default options. You can then generate a collection of dates by calling

    $dates = $Calendar->dates($startDate, $endDate)->get();

Both start and end date should be in `YYYY-MM-DD` format. If you wish to generate a html table for the dates automatically you can pass the date collection from the above example into the display method along with a formatter object.

    echo $Calendar->display(
        new \Snscripts\MyCal\Formatters\BootstrapFormatter,
        $dates
    );

## Date collection

Each item in the collection of dates is an instance of a MyCal Date object, these objects come with a few helper methods.

    $dates = $Calendar->dates($startDate, $endDate)->get();
    foreach ($dates as $Date) {

        var_dump($Date->isWeekend()); // true if date is a saturday or sunday.
        var_dump($Date->isWeekStart()); // true if date matches the week start option defined on the calendar

        echo $Date->display($format, $DateTimeZone = '');
        // $format is the date format to use as described on http://php.net/date
        // $DateTImeZone should be passed as a DateTimeZone object (http://php.net/datetimezone)
        // If omitted a DateTimeZone object is created from the timezone option defined on the calendar
    }