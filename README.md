# MyCal

[![Author](http://img.shields.io/badge/author-@mikebarlow-red.svg?style=flat-square)](https://twitter.com/mikebarlow)
[![Source Code](http://img.shields.io/badge/source-mikebarlow/mycal-brightgreen.svg?style=flat-square)](https://github.com/mikebarlow/mycal)
[![Latest Version](https://img.shields.io/github/release/mikebarlow/mycal.svg?style=flat-square)](https://github.com/mikebarlow/mycal/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/mikebarlow/mycal/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/mikebarlow/mycal/master.svg?style=flat-square)](https://travis-ci.org/mikebarlow/mycal)

## Introduction

MyCal is a PSR-2 compliant package used for generating calendars and associated events easily. The calendar also comes with easy methods to generate a table based view for the calendar. Everything is also object orientated for easy use.

## Requirements

### Composer

MyCal requires the following:

* "php": ">=5.5.0"
* "cartalyst/collections": "1.1.*",
* "snscripts/result": "1.0.*"

And the following if you wish to run in dev mode and run tests.

* "phpunit/phpunit": "~4.0"
* "squizlabs/php_codesniffer": "~2.0"

MyCal suggests the following for database integrations.

* "illuminate/database": "Use Eloquent as the method for saving your calendars / events"

## Installation

### Composer

Simplest installation is via composer.

    composer require snscripts/mycal 1.*

or adding to your projects `composer.json` file.

    {
        "require": {
            "snscripts/mycal": "1.*"
        }
    }
