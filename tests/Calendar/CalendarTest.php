<?php
namespace Snscripts\MyCal\Tests\Calendar;

use Snscripts\MyCal\Calendar\Calendar;
use Snscripts\MyCal\Interfaces\CalendarInterface;
use Snscripts\MyCal\Calendar\Date;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->DateFactoryMock = $this->createMock('\Snscripts\MyCal\DateFactory');
        $this->EventFactoryMock = $this->createMock('\Snscripts\MyCal\EventFactory');
        $this->OptionsMock = $this->createMock('\Snscripts\MyCal\Calendar\Options');
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
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
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
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $Dates = $Calendar->dates('2016-12-01', '2016-12-05')->get();

        $this->assertInstanceOf(
            'Illuminate\Support\Collection',
            $Dates
        );

        $this->assertSame(
            5,
            $Dates->count()
        );
    }

    public function testBuildThrowsExceptionWhenNoDatesSet()
    {
        $this->expectException('\BadMethodCallException');
        $this->expectExceptionMessage('Start and end dates are required');

        $CalendarIntegration = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');

        $Calendar = new Calendar(
            $CalendarIntegration,
            $this->DateFactoryMock,
            $this->OptionsMock
        );

        $Calendar->get();
    }

    public function testGetTableHeaderReturnsCorrectHtmlForHeaderRow()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $expected = '<tr class="mycal-header-row"><th class="mycal-header">Mon</th><th class="mycal-header">Tue</th><th class="mycal-header">Wed</th><th class="mycal-header">Thu</th><th class="mycal-header">Fri</th><th class="mycal-header">Sat</th><th class="mycal-header">Sun</th></tr>';

        $Formatter = new \Snscripts\MyCal\Formatters\BootstrapFormatter;
        $Formatter->setCalendar(
            $Calendar
        );

        $this->assertSame(
            $expected,
            $Calendar->getTableHeader(
                $Formatter
            )
        );
    }

    public function testGetTableHeaderReturnsCorrectHtmlForHeaderRowWhenStartingOnSunday()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
            \Snscripts\MyCal\Calendar\Options::set(['weekStartsOn' => Date::SUNDAY])
        );

        $expected = '<tr class="mycal-header-row"><th class="mycal-header">Sun</th><th class="mycal-header">Mon</th><th class="mycal-header">Tue</th><th class="mycal-header">Wed</th><th class="mycal-header">Thu</th><th class="mycal-header">Fri</th><th class="mycal-header">Sat</th></tr>';

        $Formatter = new \Snscripts\MyCal\Formatters\BootstrapFormatter;
        $Formatter->setCalendar(
            $Calendar
        );

        $this->assertSame(
            $expected,
            $Calendar->getTableHeader(
                $Formatter
            )
        );
    }

    public function testGetTableBodyCreatesCorrectHtmlBody()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $Dates = $Calendar->dates('2016-12-01', '2016-12-05')->get();

        $Formatter = new \Snscripts\MyCal\Formatters\BootstrapFormatter;
        $Formatter->setCalendar(
            $Calendar
        );

        $this->assertSame(
            '<tr class="mycal-row"><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-date"><div class="date-num"><sup>1</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>2</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>3</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>4</sup></div><div class="events"></div></td></tr><tr class="mycal-row"><td class="mycal-date"><div class="date-num"><sup>5</sup></div><div class="events"></div></td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td></tr>',
            $Calendar->getTableBody(
                $Formatter,
                $Dates
            )
        );
    }

    public function testGetTableBodyCreatesCorrectHtmlBodyWhenStartingOnSunday()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
            \Snscripts\MyCal\Calendar\Options::set(['weekStartsOn' => Date::SUNDAY])
        );

        $Dates = $Calendar->dates('2016-12-01', '2016-12-05')->get();

        $Formatter = new \Snscripts\MyCal\Formatters\BootstrapFormatter;
        $Formatter->setCalendar(
            $Calendar
        );

        $this->assertSame(
            '<tr class="mycal-row"><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-date"><div class="date-num"><sup>1</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>2</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>3</sup></div><div class="events"></div></td></tr><tr class="mycal-row"><td class="mycal-date"><div class="date-num"><sup>4</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>5</sup></div><div class="events"></div></td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td></tr>',
            $Calendar->getTableBody(
                $Formatter,
                $Dates
            )
        );
    }

    public function testGetTableWrapperReturnsContentWithTableTags()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
            \Snscripts\MyCal\Calendar\Options::set(['weekStartsOn' => Date::SUNDAY])
        );

        $Formatter = new \Snscripts\MyCal\Formatters\BootstrapFormatter;
        $Formatter->setCalendar(
            $Calendar
        );

        $this->assertSame(
            '<table class="table mycal" id="MyCal"><thead><tr><th>Test Header</th></tr></thead><tbody><tr><td>My Test Row</td></tr></tbody></table>',
            $Calendar->getTableWrapper(
                $Formatter,
                '<tr><th>Test Header</th></tr>',
                '<tr><td>My Test Row</td></tr>'
            )
        );
    }

    public function testDisplayReturnsFullHtmlCalendarTable()
    {
        $Calendar = new Calendar(
            $this->CalendarInterfaceMock,
            new \Snscripts\MyCal\DateFactory(
                $this->EventFactoryMock
            ),
            \Snscripts\MyCal\Calendar\Options::set()
        );

        $Dates = $Calendar->dates('2016-12-01', '2016-12-05')->get();

        $this->assertSame(
            '<table class="table mycal" id="MyCal"><thead><tr class="mycal-header-row"><th class="mycal-header">Mon</th><th class="mycal-header">Tue</th><th class="mycal-header">Wed</th><th class="mycal-header">Thu</th><th class="mycal-header">Fri</th><th class="mycal-header">Sat</th><th class="mycal-header">Sun</th></tr></thead><tbody><tr class="mycal-row"><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-date"><div class="date-num"><sup>1</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>2</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>3</sup></div><div class="events"></div></td><td class="mycal-date"><div class="date-num"><sup>4</sup></div><div class="events"></div></td></tr><tr class="mycal-row"><td class="mycal-date"><div class="date-num"><sup>5</sup></div><div class="events"></div></td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td><td class="mycal-empty"> &nbsp; </td></tr></tbody></table>',
            $Calendar->display(
                new \Snscripts\MyCal\Formatters\BootstrapFormatter,
                $Dates
            )
        );
    }

    public function testSaveProcessesSuccessResult()
    {
        $Result = \Snscripts\Result\Result::success()
            ->setExtra('calendar_id', 10);

        $CalendarIntegration = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $CalendarIntegration->method('save')
            ->willReturn($Result);

        $Calendar = new Calendar(
            $CalendarIntegration,
            $this->DateFactoryMock,
            $this->OptionsMock
        );
        $Calendar->setAllData([
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ],
            'options' => []
        ]);

        $SaveTest = $Calendar->save();

        $this->assertInstanceOf(
            '\Snscripts\Result\Result',
            $SaveTest
        );

        $this->assertSame(
            10,
            $Calendar->id
        );
    }

    public function testSaveProcessesFailResult()
    {
        $Result = \Snscripts\Result\Result::fail()
            ->setCode(\Snscripts\Result\Result::ERROR)
            ->setMessage('Save failed');

        $CalendarIntegration = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $CalendarIntegration->method('save')
            ->willReturn($Result);

        $Calendar = new Calendar(
            $CalendarIntegration,
            $this->DateFactoryMock,
            $this->OptionsMock
        );
        $Calendar->setAllData([
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ],
            'options' => []
        ]);

        $SaveTest = $Calendar->save();

        $this->assertInstanceOf(
            '\Snscripts\Result\Result',
            $SaveTest
        );

        $this->assertNull(
            $Calendar->id
        );

        $this->assertTrue(
            $SaveTest->isFail()
        );
    }

    public function testLoadSetsUpCalendarFromIntegration()
    {
        $Result = \Snscripts\Result\Result::success()
            ->setExtra(
                'calData',
                [
                    'id' => 10,
                    'name' => 'Test Calendar',
                    'user_id' => 1,
                    'extras' => [
                        'test' => [
                            'foo', 'bar', 'foobar', 'barfoo'
                        ]
                    ],
                    'options' => [
                        'weekStartsOn' => 1,
                        'defaultTimezone' => 'Europe/London',
                        'days' => [
                            0 => 'Sun',
                            1 => 'Mon',
                            2 => 'Tue',
                            3 => 'Wed',
                            4 => 'Thu',
                            5 => 'Fri',
                            6 => 'Sat'
                        ]
                    ]
                ]
            );

        $CalendarIntegration = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $CalendarIntegration->method('load')
            ->willReturn($Result);

        $Calendar = new Calendar(
            $CalendarIntegration,
            $this->DateFactoryMock,
            $this->OptionsMock
        );

        $Cal = $Calendar->load(10);

        $this->assertSame(
            'Test Calendar',
            $Cal->name
        );

        $this->assertSame(
            [
                'foo', 'bar', 'foobar', 'barfoo'
            ],
            $Cal->test
        );

        $this->assertSame(
            'Europe/London',
            $Cal->getOptions()->defaultTimezone
        );
    }

    public function testLoadThrowsExceptionOnError()
    {
        $this->expectException('\Snscripts\MyCal\Exceptions\NotFoundException');
        $this->expectExceptionMessage('Not loaded');

        $Result = \Snscripts\Result\Result::fail()
            ->setCode(\Snscripts\Result\Result::NOT_FOUND)
            ->setMessage('Not loaded');

        $CalendarIntegration = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $CalendarIntegration->method('load')
            ->willReturn($Result);

        $Calendar = new Calendar(
            $CalendarIntegration,
            $this->DateFactoryMock,
            $this->OptionsMock
        );

        $Cal = $Calendar->load(10);
    }
}
