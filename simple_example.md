---
layout: page
title: Simple Example
permalink: /simple-example
order: 20
---
# Simple Example

To initiate the package, setup the Calendar Factory along with the Date Factory and the calendar integration you wish to use.

If no database integrations are necessary the Null integration can be used.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Null\Calendar,
        new \Snscripts\MyCal\DateFactory(
            new \Snscripts\MyCal\EventFactory(
                new \Snscripts\MyCal\Integrations\Null\Event
            )
        )
    );

Once setup as above, load up a new calendar by using the Calendar Factory load method

    $Calendar = $CalendarFactory->load();

This creates a new Calendar object with default options. You can then generate a collection of dates by calling

    $dates = $Calendar->build($startDate, $endDate);

Both start and end date should be in `YYYY-MM-DD` format. If you wish to generate a html table for the dates automatically you can call:

    echo $Calendar->display($startDate, $endDate);

## Date collection

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