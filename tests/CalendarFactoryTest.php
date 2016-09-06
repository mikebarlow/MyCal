<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\CalendarFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\EventInterface;

class CalendarFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->EventInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\EventInterface');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\CalendarFactory',
            new CalendarFactory(
                $this->CalendarInterfaceMock,
                $this->EventInterfaceMock
            )
        );
    }

    public function testNewInstanceReturnsCalendarObject()
    {
        $Factory = new CalendarFactory(
            $this->CalendarInterfaceMock,
            $this->EventInterfaceMock
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar',
            $Factory->newInstance()
        );
    }
}
