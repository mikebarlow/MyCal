<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Interfaces\CalendarInterface;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->DateFactoryMock = $this->getMock('\Snscripts\MyCal\DateFactory');
        $this->OptionsMock = $this->getMock('\Snscripts\MyCal\Calendar\Options');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Calendar',
            new Calendar(
                $this->CalendarInterfaceMock,
                $this->DateFactoryMock,
                $this->OptionsMock
            )
        );
    }

    public function testBaseObjectGetSet()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock,
            $this->OptionsMock
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
            $this->DateFactoryMock,
            $this->OptionsMock
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
