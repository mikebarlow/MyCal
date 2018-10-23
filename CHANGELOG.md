# Change log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [0.10.0] - 2018-10-23

* Making calendar options available to Calendar and Event Integrations

## [0.9.1] - 2018-08-02

* Bug fix for updated formatters

## [0.9.0] - 2018-08-02

* Removed an unused method from Event object.
* Added method to events for checking if a Date object is within an event.
* Modified formatters to pass Date object into parseEvents method.

## [0.8.0] - 2018-08-01

* Converted to use Laravel collections.

## [0.7.0] - 2017-07-01

* Added polymorphic columns to events for extendability.

## [0.6.0] - 2017-06-26

* Added `isToday()` method to Date object to return boolean if today matches Date object.
* Added `today` CSS class to formatter to allow highlighting of current date when generating html table.

## [0.5.0] - 2017-05-16

* Added `attachEvent()` method to Date object to allow adding manual events easier.
* Changed the API for generating date collections
* Changed how to display a calendar
* Added Formatters for controlling how calendar display looks

## [0.4.0] - 2017-04-30

* Saving and loading of events

## [0.3.0] - 2017-01-16

* Added key of the date to Dates Collection in the format of YYYY-MM-DD.
* Amended how the date range is loaded so it's loaded in Calendar Timezone rather then UTC. Ensures the correct dates for the given Calendar is loaded.

## [0.2.0] - 2017-01-14

* Added service container binding for Laravel
* Fixed strict checking bugs

## [0.1.0] - 2017-01-02

* First release of calendar package.
* Easily return collection of dates for calendar or display HTML table.
* Currently only integrates into Laravel / Eloquent for saving calendar options / data.
* Object orientated design.
* Unit tests covering the majority of the current package.
