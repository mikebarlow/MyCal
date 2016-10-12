<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\CalendarFactory;
use Snscripts\MyCal\Interfaces\CalendarInterface;

class CalendarFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\CalendarFactory',
            new CalendarFactory(
                $this->CalendarInterfaceMock
            )
        );
    }

    public function testNewInstanceReturnsCalendarObject()
    {
        $Factory = new CalendarFactory(
            $this->CalendarInterfaceMock
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Calendar',
            $Factory->newInstance()
        );
    }
}
