<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\CalendarFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;

class CalendarFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->DateFactoryMock = $this->createMock('\Snscripts\MyCal\DateFactory');
        $this->EventFactoryMock = $this->createMock('\Snscripts\MyCal\EventFactory');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\CalendarFactory',
            new CalendarFactory(
                $this->CalendarInterfaceMock,
                $this->DateFactoryMock,
                $this->EventFactoryMock
            )
        );
    }

    public function testNewInstanceReturnsCalendarObject()
    {
        $Factory = new CalendarFactory(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock,
            $this->EventFactoryMock
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Calendar',
            $Factory->load()
        );
    }
}
