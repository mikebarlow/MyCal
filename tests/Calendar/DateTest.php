<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar\Date;
use DateTimeZone;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        date_default_timezone_set('UTC');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Date',
            new Date(
                time(),
                new DateTimeZone('Europe/London'),
                Date::MONDAY
            )
        );
    }

    public function testDisplayMethodCorrectlyOutputsDate()
    {
        $time = mktime('12', '00', '00', '10', '23', '2016');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY
        );

        $this->assertSame(
            '2016-10-23 13:00:00',
            $Date->display('Y-m-d H:i:s')
        );

        $Date = new Date(
            $time,
            new DateTimeZone('America/New_York'),
            Date::MONDAY
        );

        $this->assertSame(
            '2016-10-23 08:00:00',
            $Date->display('Y-m-d H:i:s')
        );
    }

    public function testDisplayMethodsCorrectlyOutputsDateWithExtraTimeZone()
    {
        $time = mktime('12', '00', '00', '10', '23', '2016');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY
        );

        $this->assertSame(
            '2016-10-23 08:00:00',
            $Date->display(
                'Y-m-d H:i:s',
                new DateTimeZone('America/New_York')
            )
        );

        $Date = new Date(
            $time,
            new DateTimeZone('America/New_York'),
            Date::MONDAY
        );

        $this->assertSame(
            '2016-10-23 14:00:00',
            $Date->display(
                'Y-m-d H:i:s',
                new DateTimeZone('Europe/Berlin')
            )
        );
    }

    public function testIsWeekStartReturnsTrueWhenWeekStartMatches()
    {
        // 24/10/2016 is a Monday - Date is marked for week start of Monday
        $time = mktime('12', '00', '00', '10', '24', '2016');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY
        );

        $this->assertTrue(
            $Date->isWeekStart()
        );
    }

    public function testIsWeekStartReturnsFalseWhenWeekStartDoesNotMatch()
    {
        // 23/10/2016 is a Sunday - Date is marked for week start of Monday
        $time = mktime('12', '00', '00', '10', '23', '2016');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY
        );

        $this->assertFalse(
            $Date->isWeekStart()
        );
    }

    public function testIsWeekendReturnsCorrectValue()
    {
        // 24/10/2016 is a Monday
        $time = mktime('12', '00', '00', '10', '24', '2016');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY
        );

        $this->assertFalse(
            $Date->isWeekend()
        );

        // 23/10/2016 is a Sunday
        $time = mktime('12', '00', '00', '10', '23', '2016');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY
        );

        $this->assertTrue(
            $Date->isWeekend()
        );
    }
}
