# Change log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
