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

}
