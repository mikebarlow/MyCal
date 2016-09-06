<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Event;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\EventInterface;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->EventInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\EventInterface');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Event',
            new Event(
                $this->CalendarInterfaceMock,
                $this->EventInterfaceMock
            )
        );
    }

    public function testBaseObjectGetSet()
    {
        $Event = new Event(
            $this->CalendarInterfaceMock,
            $this->EventInterfaceMock
        );

        $Event->name = 'My Event';
        $Event->location = 'UK';
        $Event->date = '2016-09-06';

        $this->assertSame(
            'My Event',
            $Event->name
        );

        $this->assertSame(
            'UK',
            $Event->location
        );

        $this->assertSame(
            '2016-09-06',
            $Event->date
        );
    }

    public function testBaseObjectToArrayAndToJson()
    {
        $Event = new Event(
            $this->CalendarInterfaceMock,
            $this->EventInterfaceMock
        );

        $Event->name = 'My Event';
        $Event->location = 'UK';
        $Event->date = '2016-09-06';

        $this->assertSame(
            [
                'name' => 'My Event',
                'location' => 'UK',
                'date' => '2016-09-06'
            ],
            $Event->toArray()
        );

        $this->assertSame(
            '{"name":"My Event","location":"UK","date":"2016-09-06"}',
            $Event->toJson()
        );

        $this->assertSame(
            '{"name":"My Event","location":"UK","date":"2016-09-06"}',
            $Event->__toString()
        );
    }
}
