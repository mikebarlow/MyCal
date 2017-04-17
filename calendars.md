---
layout: page
title: Calendars
permalink: /calendars
order: 40
---
# Calendars

To get started, initiate the Calendar factory along with the Date and Events factories and your chosen integration. Currently only Laravel Eloquent Integration is available.

    $CalendarFactory = new \Snscripts\MyCal\CalendarFactory(
        new \Snscripts\MyCal\Integrations\Eloquent\Calendar,
        new \Snscripts\MyCal\DateFactory(
            new \Snscripts\MyCal\EventFactory(
                new \Snscripts\MyCal\Integrations\Eloquent\Event
            )
        )
    );