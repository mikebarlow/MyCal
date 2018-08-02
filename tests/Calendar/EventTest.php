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

    public function testGenerateDateTimeReturnsCorrectTime()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $utcDate = $event->generateDateTime(
            [
                'date' => '2017-01-19',
                'time' => '06:50:00'
            ],
            new \DateTimeZone('America/New_York')
        );

        $this->assertSame(
            '2017-01-19 11:50:00',
            $utcDate
        );
    }

    public function testGenerateDateTimeThrowsBadMethodExceptions()
    {
        $this->expectException(\BadMethodCallException::class);

        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $event->generateDateTime(
            [
                'date' => ''
            ],
            new \DateTimeZone('America/New_York')
        );
    }

    public function testGenerateDateTimeThrowsInvalidArgumentExceptionsForInvalidDates()
    {
        $this->expectException(\InvalidArgumentException::class);

        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $event->generateDateTime(
            [
                'date' => '01-02-2017',
                'time' => '00:00:00'
            ],
            new \DateTimeZone('America/New_York')
        );
    }

    public function testGenerateDateTimeThrowsInvalidArgumentExceptionsForInvalidTimes()
    {
        $this->expectException(\InvalidArgumentException::class);

        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $event->generateDateTime(
            [
                'date' => '2017-01-01',
                'time' => '7am'
            ],
            new \DateTimeZone('America/New_York')
        );
    }

    public function testDisplayDateReturnsCorrectDateGivenUnixTimestamp()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $this->assertSame(
            '2017-01-19 13:48:00',
            $event->displayDate(
                'Y-m-d H:i:s',
                '2017-01-19 18:48:00',
                new \DateTimeZone('America/New_York')
            )
        );

        $this->assertSame(
            '2017-01-19 19:48:00',
            $event->displayDate(
                'Y-m-d H:i:s',
                '2017-01-19 18:48:00',
                new \DateTimeZone('Europe/Berlin')
            )
        );
    }

    public function testDisplayStartReturnsCorrectDate()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $event->startsOn('2017-01-29')
            ->startsAt('09:00:00');

        $this->assertSame(
            '29/01/2017 9:00am',
            $event->displayStart('d/m/Y g:ia')
        );
    }

    public function testDisplayEndReturnsCorrectDate()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $event->endsOn('2017-01-29')
            ->endsAt('17:00:00');

        $this->assertSame(
            '29/01/2017 5:00pm',
            $event->displayEnd('d/m/Y g:ia')
        );
    }

    public function testIsStartBeforeEnd()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $this->assertTrue(
            $event->isStartBeforeEnd(
                '2017-01-19 13:48:00',
                '2017-01-20 13:48:00'
            )
        );

        $this->assertFalse(
            $event->isStartBeforeEnd(
                '2017-01-19 13:48:00',
                '2017-01-17 13:48:00'
            )
        );

        $this->assertTrue(
            $event->isStartBeforeEnd(
                '2017-01-19 13:48:00',
                null
            )
        );
    }

    public function testLoadSetsUpEventFromIntegration()
    {
        $Result = \Snscripts\Result\Result::success()
            ->setExtra(
                'eventData',
                [
                    'id' => 50,
                    'name' => 'Test Event',
                    'start_date' => '2017-01-29 17:30:00',
                    'end_date' => '2017-01-29 20:30:00',
                    'calendar_id' => 1,
                    'extras' => [
                        'author' => 'mike',
                        'foo' => 'bar',
                        'stuff' => [
                            'foo',
                            'bar',
                            'barfoo1'
                        ]
                    ]
                ]
            );

        $eventIntegration = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');
        $eventIntegration->method('load')
            ->willReturn($Result);

        $event = new Event(
            $eventIntegration,
            new \DateTimeZone('Europe/London')
        );

        $event = $event->load(10);

        $this->assertSame(
            'Test Event',
            $event->name
        );

        $this->assertSame(
            [
                'foo', 'bar', 'barfoo1'
            ],
            $event->stuff
        );
    }

    public function testisDateWithinEventCalculatesInsideDate()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('UTC')
        );

        $event->start_date = '2018-08-10 12:00:00';
        $event->end_date = '2018-08-17 12:00:00';

        $insideDate = new Date(
            mktime('15', '00', '00', '08', '14', '2018'),
            new \DateTimeZone('UTC'),
            1
        );

        $this->assertTrue(
            $event->isDateWithinEvent($insideDate)
        );
    }

    public function testisDateWithinEventCalculatesOutsideDate()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('UTC')
        );

        $event->start_date = '2018-08-10 12:00:00';
        $event->end_date = '2018-08-17 12:00:00';

        $outsideDate = new Date(
            mktime('15', '00', '00', '08', '25', '2018'),
            new \DateTimeZone('UTC'),
            1
        );

        $justOutDate = new Date(
            mktime('11', '00', '00', '08', '10', '2018'),
            new \DateTimeZone('UTC'),
            1
        );

        $this->assertFalse(
            $event->isDateWithinEvent($outsideDate)
        );

        $this->assertFalse(
            $event->isDateWithinEvent($justOutDate)
        );
    }

    public function testisDateWithinEventCalculatesExactDates()
    {
        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('UTC')
        );

        $event->start_date = '2018-08-10 12:00:00';
        $event->end_date = '2018-08-17 12:00:00';

        $exactDate1 = new Date(
            mktime('12', '00', '00', '08', '10', '2018'),
            new \DateTimeZone('UTC'),
            1
        );
        $exactDate2 = new Date(
            mktime('12', '00', '00', '08', '17', '2018'),
            new \DateTimeZone('UTC'),
            1
        );

        $this->assertTrue(
            $event->isDateWithinEvent($exactDate1)
        );

        $this->assertTrue(
            $event->isDateWithinEvent($exactDate2)
        );
    }

    public function testisDateWithinEventHandlesNoDatesSet()
    {
        $this->expectException(\InvalidArgumentException::class);

        $event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('UTC')
        );

        $date = new Date(
            mktime('12', '00', '00', '08', '10', '2018'),
            new \DateTimeZone('UTC'),
            1
        );

        $event->isDateWithinEvent($date);
    }
}
