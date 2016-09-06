<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Interfaces\EventInterface;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->EventInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\EventInterface');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar',
            new Calendar(
                $this->CalendarInterfaceMock,
                $this->EventInterfaceMock
            )
        );
    }

    public function testBaseObjectGetSet()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            $this->EventInterfaceMock
        );

        $Calendar->name = 'My Calendar';
        $Calendar->author = 'Mike';

        $this->assertSame(
            'My Calendar',
            $Calendar->name
        );

        $this->assertSame(
            'Mike',
            $Calendar->author
        );
    }

    public function testBaseObjectToArrayAndToJson()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            $this->EventInterfaceMock
        );

        $Calendar->name = 'My Calendar';
        $Calendar->author = 'Mike';

        $this->assertSame(
            [
                'name' => 'My Calendar',
                'author' => 'Mike'
            ],
            $Calendar->toArray()
        );

        $this->assertSame(
            '{"name":"My Calendar","author":"Mike"}',
            $Calendar->toJson()
        );

        $this->assertSame(
            '{"name":"My Calendar","author":"Mike"}',
            $Calendar->__toString()
        );
    }

}
