<?php
namespace Snscripts\MyCal\Tests\Calendar;

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

    public function testGetOptionsReturnsOptionObject()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Options',
            $Calendar->getOptions()
        );

        $this->assertSame(
            [
                'weekStartsOn' => 1,
                'defaultTimezone' => 'Europe/London',
                'displayTable' => [
                    'tableClass' => 'table mycal',
                    'tableId' => 'MyCal',
                    'headerRowClass' => 'mycal-header-row',
                    'headerClass' => 'mycal-header',
                    'rowClass' => 'mycal-row',
                    'dateClass' => 'mycal-date',
                    'emptyClass' => 'mycal-empty'
                ],
                'days' => [
                    0 => 'Sun',
                    1 => 'Mon',
                    2 => 'Tue',
                    3 => 'Wed',
                    4 => 'Thu',
                    5 => 'Fri',
                    6 => 'Sat'
                ]
            ],
            $Calendar->getOptions()->toArray()
        );
    }

    public function testSetOptionsSetsOptionObjectCorrectly()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $Options = \Snscripts\MyCal\Calendar\Options::set([
            'weekStartsOn' => Date::SUNDAY,
            'defaultTimezone' => 'America/New_York'
        ]);

        $Calendar->setOptions($Options);

        $this->assertInstanceOf(
            'Snscripts\MyCal\Calendar\Options',
            $Calendar->getOptions()
        );

        $this->assertSame(
            [
                'weekStartsOn' => 0,
                'defaultTimezone' => 'America/New_York',
                'displayTable' => [
                    'tableClass' => 'table mycal',
                    'tableId' => 'MyCal',
                    'headerRowClass' => 'mycal-header-row',
                    'headerClass' => 'mycal-header',
                    'rowClass' => 'mycal-row',
                    'dateClass' => 'mycal-date',
                    'emptyClass' => 'mycal-empty'
                ],
                'days' => [
                    0 => 'Sun',
                    1 => 'Mon',
                    2 => 'Tue',
                    3 => 'Wed',
                    4 => 'Thu',
                    5 => 'Fri',
                    6 => 'Sat'
                ]
            ],
            $Calendar->getOptions()->toArray()
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

    public function testBuildReturnsCollectionOfDates()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $Dates = $Calendar->build('2016-12-01', '2016-12-05');

        $this->assertInstanceOf(
            'Cartalyst\Collections\Collection',
            $Dates
        );

        $this->assertSame(
            5,
            $Dates->count()
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

    public function testGetTableBodyCreatesCorrectHtmlBody()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $Dates = $Calendar->build('2016-12-01', '2016-12-05');

        $this->assertSame(
            '<tbody><tr class="mycal-row"><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-date">1</td><td class="mycal-date">2</td><td class="mycal-date">3</td><td class="mycal-date">4</td></tr><tr class="mycal-row"><td class="mycal-date">5</td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td></tr></tbody>',
            $Calendar->getTableBody($Dates)
        );
    }

    public function testGetTableBodyCreatesCorrectHtmlBodyWhenStartingOnSunday()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set(['weekStartsOn' => Date::SUNDAY])
        );

        $Dates = $Calendar->build('2016-12-01', '2016-12-05');

        $this->assertSame(
            '<tbody><tr class="mycal-row"><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-date">1</td><td class="mycal-date">2</td><td class="mycal-date">3</td></tr><tr class="mycal-row"><td class="mycal-date">4</td><td class="mycal-date">5</td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td></tr></tbody>',
            $Calendar->getTableBody($Dates)
        );
    }

    public function testGetTableWrapperReturnsContentWithTableTags()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set(['weekStartsOn' => Date::SUNDAY])
        );

        $this->assertSame(
            '<table class="table mycal" id="MyCal"><tr><td>My Test Row</td></tr></table>',
            $Calendar->getTableWrapper('<tr><td>My Test Row</td></tr>')
        );
    }

    public function testDisplayReturnsFullHtmlCalendarTable()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory,
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $this->assertSame(
            '<table class="table mycal" id="MyCal"><thead><tr class="mycal-header-row"><td class="mycal-header">Mon</td><td class="mycal-header">Tue</td><td class="mycal-header">Wed</td><td class="mycal-header">Thu</td><td class="mycal-header">Fri</td><td class="mycal-header">Sat</td><td class="mycal-header">Sun</td></tr></thead><tbody><tr class="mycal-row"><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-date">1</td><td class="mycal-date">2</td><td class="mycal-date">3</td><td class="mycal-date">4</td></tr><tr class="mycal-row"><td class="mycal-date">5</td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td></tr></tbody></table>',
            $Calendar->display('2016-12-01', '2016-12-05')
        );
    }
}
