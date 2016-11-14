<?php
namespace Snscripts\MyCal\Tests;

use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Calendar\Date;

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

    public function testGetRangeReturnsCorrectDateRange()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $this->assertInstanceOf(
            'DatePeriod',
            $Calendar->getRange('2016-11-01', '2016-11-30')
        );
    }

    public function testProcessDateRangeReturnsArrayOfDateObjects()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $generated = $Calendar->processDateRange(
            $Calendar->getRange('2016-11-01', '2016-11-05')
        );
        $dates = [];
        foreach ($generated as $Date) {
            $this->assertInstanceOf(
                'Snscripts\MyCal\Calendar\Date',
                $Date
            );
            $dates[] = $Date->display('Y-m-d');
        }

        $this->assertTrue(
            is_array($generated)
        );

        $this->assertSame(
            ['2016-11-01', '2016-11-02', '2016-11-03', '2016-11-04', '2016-11-05'],
            $dates
        );
    }

    public function testGetTableHeaderReturnsCorrectHtmlForHeaderRow()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $expected = '<thead><tr class="mycal-header-row"><td class="mycal-header">Mon</td><td class="mycal-header">Tue</td><td class="mycal-header">Wed</td><td class="mycal-header">Thu</td><td class="mycal-header">Fri</td><td class="mycal-header">Sat</td><td class="mycal-header">Sun</td></tr></thead>';

        $this->assertSame(
            $expected,
            $Calendar->getTableHeader()
        );
    }

    public function testGetTableHeaderReturnsCorrectHtmlForHeaderRowWhenStartingOnSunday()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set(['weekStartsOn' => Date::SUNDAY])
        );

        $expected = '<thead><tr class="mycal-header-row"><td class="mycal-header">Sun</td><td class="mycal-header">Mon</td><td class="mycal-header">Tue</td><td class="mycal-header">Wed</td><td class="mycal-header">Thu</td><td class="mycal-header">Fri</td><td class="mycal-header">Sat</td></tr></thead>';

        $this->assertSame(
            $expected,
            $Calendar->getTableHeader()
        );
    }
}
