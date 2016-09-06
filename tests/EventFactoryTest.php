<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\EventInterface;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->EventInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\EventInterface');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\EventFactory',
            new EventFactory(
                $this->CalendarInterfaceMock,
                $this->EventInterfaceMock
            )
        );
    }

    public function testNewInstanceReturnsEventObject()
    {
        $Factory = new EventFactory(
            $this->CalendarInterfaceMock,
            $this->EventInterfaceMock
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Event',
            $Factory->newInstance()
        );
    }
}
