<?php
namespace Snscripts\MyCal\Tests\Calendar;

use Snscripts\MyCal\Calendar\Event;
use Snscripts\MyCal\Interfaces\EventInterface;
use Snscripts\MyCal\Calendar\Date;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->EventInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Event',
            new Event(
                $this->EventInterfaceMock,
                new \DateTimeZone('Europe/London')
            )
        );
    }

    public function testGenerateTimestampReturnsCorrectTime()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $timestamp = $Event->generateTimestamp(
            [
                'date' => '2017-01-19',
                'time' => '06:50:00'
            ],
            new \DateTimeZone('America/New_York')
        );

        $this->assertSame(
            1484826600,
            $timestamp
        );

        $this->assertSame(
            '2017-01-19 11:50:00',
            date('Y-m-d H:i:s', $timestamp)
        );
    }

    public function testGenerateTimestampThrowsBadMethodExceptions()
    {
        $this->expectException(\BadMethodCallException::class);

        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->generateTimestamp(
            [
                'date' => ''
            ],
            new \DateTimeZone('America/New_York')
        );
    }

    public function testGenerateTimestampThrowsInvalidArgumentExceptionsForInvalidDates()
    {
        $this->expectException(\InvalidArgumentException::class);

        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->generateTimestamp(
            [
                'date' => '01-02-2017',
                'time' => '00:00:00'
            ],
            new \DateTimeZone('America/New_York')
        );
    }

    public function testGenerateTimestampThrowsInvalidArgumentExceptionsForInvalidTimes()
    {
        $this->expectException(\InvalidArgumentException::class);

        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->generateTimestamp(
            [
                'date' => '2017-01-01',
                'time' => '7am'
            ],
            new \DateTimeZone('America/New_York')
        );
    }

    public function testDisplayDateReturnsCorrectDateGivenUnixTimestamp()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $this->assertSame(
            '2017-01-19 13:48:00',
            $Event->displayDate(
                'Y-m-d H:i:s',
                1484851680,
                new \DateTimeZone('America/New_York')
            )
        );

        $this->assertSame(
            '2017-01-19 19:48:00',
            $Event->displayDate(
                'Y-m-d H:i:s',
                1484851680,
                new \DateTimeZone('Europe/Berlin')
            )
        );
    }

    public function testIsStartBeforeEnd()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $this->assertTrue(
            $Event->isStartBeforeEnd(
                1484851680,
                1484938080
            )
        );

        $this->assertFalse(
            $Event->isStartBeforeEnd(
                1484938080,
                1484851680
            )
        );

        $this->assertTrue(
            $Event->isStartBeforeEnd(
                1484938080,
                null
            )
        );
    }

    public function testPrepareEventGeneratesCollectionWithOneItemForSingleDayEvent()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('Europe/London')
        );

        $Event->startsOn('2017-01-23')
            ->startsAt('21:55:00')
            ->endsOn('2017-01-23')
            ->endsAt('22:55:00');
        $Event->name = 'Test Event';

        $Events = $Event->prepareEvent(
            '2017-01-23',
            '2017-01-23'
        );

        $this->assertSame(
            1,
            $Events->count()
        );
    }

    public function testPrepareEventGeneratesCollectionWithMultipleItemsForMultieDayEvent()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('Europe/London')
        );

        $Event->startsOn('2017-01-23')
            ->startsAt('09:00:00')
            ->endsOn('2017-01-24')
            ->endsAt('17:00:00');
        $Event->name = 'Test Event';

        $Events = $Event->prepareEvent(
            '2017-01-23',
            '2017-01-24'
        );

        $this->assertSame(
            2,
            $Events->count()
        );

        $Event->startsOn('2017-01-23')
            ->startsAt('09:00:00')
            ->endsOn('2017-01-25')
            ->endsAt('17:00:00');
        $Event->name = 'Test Event';

        $Events = $Event->prepareEvent(
            '2017-01-23',
            '2017-01-25'
        );

        $this->assertSame(
            3,
            $Events->count()
        );
    }
}
