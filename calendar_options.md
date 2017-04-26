---
layout: page
title: Calendar Options
permalink: /calendar-options
order: 45
---
# Calendar Options

As shown on the [calendar documentation](/MyCal/calendars), when loading a Calendar you can set a new Options object and redefine the options you want:

    $Calendar = $CalendarFactory->load(
        null,
        \Snscripts\MyCal\Calendar\Options::set([
            'defaultTimezone' => 'America/New_York'
        ])
    );

Below is a table of all the currently available options and their default values, dot notation is used to denote sub-arrays.

Option Name | Default Value | Type
------------|---------------|-----
weekStartsOn| 1 | int
defaultTimezone| 'Europe/London'| string
displayTable.tableClass | 'table mycal' | string
displayTable.tableId | 'MyCal' | string 
displayTable.headerRowClass | 'mycal-header-row' | string
displayTable.headerClass | 'mycal-header' | string
displayTable.rowClass | 'mycal-row' | string
displayTable.dateClass | 'mycal-date' | string
displayTable.emptyClass | 'mycal-empty' | string
days.0 | 'Sun' | string
days.1 | 'Mon' | string
days.2 | 'Tue' | string
days.3 | 'Wed' | string
days.4 | 'Thu' | string
days.5 | 'Fri' | string
days.6 | 'Sat' | string