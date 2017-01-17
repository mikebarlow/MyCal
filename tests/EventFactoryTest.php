<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\EventFactory;
use Snscripts\MyCal\Interfaces\EventInterface;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->EventInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\EventFactory',
            new EventFactory(
                $this->EventInterfaceMock
            )
        );
    }

    public function testNewInstanceReturnsCalendarObject()
    {
        $Factory = new EventFactory(
            $this->EventInterfaceMock
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Event',
            $Factory->load()
        );
    }
}
