---
layout: page
title: Installation
permalink: /installation
order: 20
---
# Requirements

MyCal requires the following:

* "php": ">=5.6.0"
* "cartalyst/collections": "1.1.*",
* "snscripts/result": "1.0.*"

And the following if you wish to run in dev mode and run tests.

* "phpunit/phpunit": "~5.7"
* "squizlabs/php_codesniffer": "~2.0"

MyCal suggests the following for database integrations.

* "illuminate/database": "Use Eloquent as the method for saving your calendars / events"

# Installation

Simplest installation is via composer.

    composer require snscripts/mycal 0.*

or adding to your projects `composer.json` file.

    {
        "require": {
            "snscripts/mycal": "0.*"
        }
    }

## Laravel

If you are using the Laravel Eloquent Integration then you will need add the MyCal Service Provider to your `config/app.php` file within the providers section.

    'providers' => [
        /*
         * Package Service Providers...
         */
        Snscripts\MyCal\Integrations\Eloquent\MyCalServiceProvider::class
    ]

Next the tables that hold the calendar and event data need to be created. Run the standard artisan migration command.

    php artisan migrate

## Standalone Eloquent

If you are using Laravel Eloquent outside of Laravel then you will need to create the MySQL tables manually, here is an SQL dump of tables needed.

    -- Create syntax for TABLE 'calendar_extras'
    CREATE TABLE `calendar_extras` (
      `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `value` text COLLATE utf8_unicode_ci NOT NULL,
      `calendar_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`slug`,`calendar_id`),
      KEY `calendar_extras_calendar_id_foreign` (`calendar_id`),
      CONSTRAINT `calendar_extras_calendar_id_foreign` FOREIGN KEY (`calendar_id`) REFERENCES `calendars` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- Create syntax for TABLE 'calendars'
    CREATE TABLE `calendars` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `user_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- Create syntax for TABLE 'options'
    CREATE TABLE `options` (
      `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `value` text COLLATE utf8_unicode_ci NOT NULL,
      `calendar_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`slug`,`calendar_id`),
      KEY `options_calendar_id_foreign` (`calendar_id`),
      CONSTRAINT `options_calendar_id_foreign` FOREIGN KEY (`calendar_id`) REFERENCES `calendars` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- Create syntax for TABLE 'event_extras'
    CREATE TABLE `event_extras` (
      `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `value` text COLLATE utf8_unicode_ci NOT NULL,
      `event_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`slug`,`event_id`),
      KEY `event_extras_event_id_foreign` (`event_id`),
      CONSTRAINT `event_extras_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- Create syntax for TABLE 'events'
    CREATE TABLE `events` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `start_date` datetime NOT NULL,
      `end_date` datetime NOT NULL,
      `calendar_id` int(10) unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `events_calendar_id_foreign` (`calendar_id`),
      CONSTRAINT `events_calendar_id_foreign` FOREIGN KEY (`calendar_id`) REFERENCES `calendars` (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

