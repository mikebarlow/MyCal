<?php
namespace Snscripts\MyCal\Tests\Calendar;

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

    public function testNewEventSetsUpNewEvent()
    {
        $EventMock = new \Snscripts\MyCal\Calendar\Event(
            $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface'),
            new \DateTimeZone('Europe/London')
        );

        $EventFactory = $this->createMock('\Snscripts\MyCal\EventFactory');
        $EventFactory->method('load')
            ->willReturn($EventMock);

        $time = mktime('12', '00', '00', '01', '29', '2017');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY,
            $EventFactory
        );

        $Event = $Date->newEvent();

        $this->assertInstanceOf(
            '\Snscripts\MyCal\Calendar\Event',
            $Event
        );

        $this->assertSame(
            '2017-01-29',
            $Event->displayStart('Y-m-d')
        );
    }

    public function testNewEventSetsUpNewEventWithCalendar()
    {
        $EventMock = new \Snscripts\MyCal\Calendar\Event(
            $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface'),
            new \DateTimeZone('Europe/London')
        );

        $EventFactory = $this->createMock('\Snscripts\MyCal\EventFactory');
        $EventFactory->method('load')
            ->willReturn($EventMock);

        $CalOptions = $this->createMock('\Snscripts\MyCal\Calendar\Options');
        $CalOptions->method('__get')
            ->with('defaultTimezone')
            ->willReturn('America/New_York');

        $Calendar = $this->createMock('\Snscripts\MyCal\Calendar\Calendar');
        $Calendar->method('getOptions')
            ->willReturn($CalOptions);

        $time = mktime('12', '00', '00', '01', '29', '2017');
        $Date = new Date(
            $time,
            new DateTimeZone('Europe/London'),
            Date::MONDAY,
            $EventFactory
        );
        $Date->setCalendar($Calendar);

        $Event = $Date->newEvent();

        $this->assertInstanceOf(
            '\Snscripts\MyCal\Calendar\Event',
            $Event
        );

        // timezone test - this should prove the calendar was set
        // along with it's timezone
        $this->assertSame(
            '2017-01-28 19:00:00',
            $Event->displayStart('Y-m-d H:i:s')
        );
    }
}
