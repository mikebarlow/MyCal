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
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $utcDate = $Event->generateDateTime(
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

        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->generateDateTime(
            [
                'date' => ''
            ],
            new \DateTimeZone('America/New_York')
        );
    }

    public function testGenerateDateTimeThrowsInvalidArgumentExceptionsForInvalidDates()
    {
        $this->expectException(\InvalidArgumentException::class);

        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->generateDateTime(
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

        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->generateDateTime(
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
                '2017-01-19 18:48:00',
                new \DateTimeZone('America/New_York')
            )
        );

        $this->assertSame(
            '2017-01-19 19:48:00',
            $Event->displayDate(
                'Y-m-d H:i:s',
                '2017-01-19 18:48:00',
                new \DateTimeZone('Europe/Berlin')
            )
        );
    }

    public function testDisplayStartReturnsCorrectDate()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->startsOn('2017-01-29')
            ->startsAt('09:00:00');

        $this->assertSame(
            '29/01/2017 9:00am',
            $Event->displayStart('d/m/Y g:ia')
        );
    }

    public function testDisplayEndReturnsCorrectDate()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('America/New_York')
        );

        $Event->endsOn('2017-01-29')
            ->endsAt('17:00:00');

        $this->assertSame(
            '29/01/2017 5:00pm',
            $Event->displayEnd('d/m/Y g:ia')
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
                '2017-01-19 13:48:00',
                '2017-01-20 13:48:00'
            )
        );

        $this->assertFalse(
            $Event->isStartBeforeEnd(
                '2017-01-19 13:48:00',
                '2017-01-17 13:48:00'
            )
        );

        $this->assertTrue(
            $Event->isStartBeforeEnd(
                '2017-01-19 13:48:00',
                null
            )
        );
    }

    public function testPrepareEventThrowsExceptionWhenEndDateIsBeforeStartDate()
    {
        $Event = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone('Europe/London')
        );

        $Event->startsOn('2017-01-23')
            ->startsAt('21:55:00')
            ->endsOn('2017-01-23')
            ->endsAt('19:55:00');
        $Event->name = 'Test Event';

        try {
            $Events = $Event->prepareEvent(
                '2017-01-23',
                '2017-01-23'
            );
        } catch (\UnexpectedValueException $e) {
            $this->assertSame(
                'The event end date can not occur before event start date',
                $e->getMessage()
            );
        }

        $Event->startsOn('2017-01-23')
            ->startsAt('21:55:00')
            ->endsOn('2017-01-20')
            ->endsAt('19:55:00');
        $Event->name = 'Test Event';

        try {
            $Events = $Event->prepareEvent(
                '2017-01-23',
                '2017-01-20'
            );
        } catch (\UnexpectedValueException $e) {
            $this->assertSame(
                'The event end date can not occur before event start date',
                $e->getMessage()
            );
        }
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

        $EventIntegration = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');
        $EventIntegration->method('load')
            ->willReturn($Result);

        $Event = new Event(
            $EventIntegration,
            new \DateTimeZone('Europe/London')
        );

        $Event = $Event->load(10);

        $this->assertSame(
            'Test Event',
            $Event->name
        );

        $this->assertSame(
            [
                'foo', 'bar', 'barfoo1'
            ],
            $Event->stuff
        );
    }
}
