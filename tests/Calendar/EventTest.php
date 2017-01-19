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
}
