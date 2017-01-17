<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\DateFactory;
use Snscripts\MyCal\Calendar\Date;
use DateTimeZone;

class DateFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->EventFactoryMock = $this->createMock('\Snscripts\MyCal\EventFactory');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\DateFactory',
            new DateFactory(
                $this->EventFactoryMock
            )
        );
    }

    public function testNewInstanceReturnsDateObject()
    {
        $Factory = new DateFactory(
            $this->EventFactoryMock
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Date',
            $Factory->newInstance(
                time(),
                new DateTimeZone('Europe/London'),
                Date::MONDAY
            )
        );
    }
}
