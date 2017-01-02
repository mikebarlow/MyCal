<?php

namespace Snscripts\MyCal\Tests\Integrations\Eloquent;

use Snscripts\MyCal\Integrations\Eloquent\Calendar as CalendarIntegration;
use Snscripts\MyCal\Calendar\Calendar;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->DateFactoryMock = $this->createMock('\Snscripts\MyCal\DateFactory');
        $this->OptionsMock = $this->createMock('\Snscripts\MyCal\Calendar\Options');
        $this->OptionsMock->method('toArray')
            ->willReturn([]);

        $this->calendarObj = new Calendar(
            $this->CalendarInterfaceMock,
            $this->DateFactoryMock,
            $this->OptionsMock
        );
    }

    public function testGetCalendarDataExtractsCalendarData()
    {
        $this->calendarObj->name = 'Test Calendar';
        $this->calendarObj->user_id = 1;
        $this->calendarObj->test = [
            'foo' => 'bar',
            'foobar' => 'barfoo'
        ];

        $CalendarIntegration = new CalendarIntegration;

        $this->assertSame(
            [
                'id' => null,
                'name' => 'Test Calendar',
                'user_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ],
                'options' => []
            ],
            $CalendarIntegration->getCalendarData(
                $this->calendarObj
            )
        );

        $this->calendarObj->id = 12;
        $this->assertSame(
            [
                'id' => 12,
                'name' => 'Test Calendar',
                'user_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ],
                'options' => []
            ],
            $CalendarIntegration->getCalendarData(
                $this->calendarObj
            )
        );
    }

    public function testSetupModelCreatesBlankEloquentModel()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;
        $Model = $CalendarIntegration->setupModel(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Calendar,
            $data
        );

        $this->assertInstanceOf(
            '\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar',
            $Model
        );

        $this->assertSame(
            'Test Calendar',
            $Model->name
        );
    }

    public function testSetupModelReturnsCalendarModel()
    {
        $data = [
            'id' => 1,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
                'test' => [
                    'foo',
                    'bar',
                    'foobar',
                    'barfoo'
                ]
            ],
            'options' => []
        ];

        $Calendar = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar')
            ->setMethods(null)
            ->getMock();
        $Calendar->setRawAttributes($data);

        $CalModel = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar')
            ->setMethods(['find'])
            ->getMock();

        $CalModel->expects($this->once())
             ->method('find')
             ->willReturn($Calendar);

        $CalendarIntegration = new CalendarIntegration;
        $Model = $CalendarIntegration->setupModel(
            $CalModel,
            $data
        );

        $this->assertInstanceOf(
            '\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar',
            $Model
        );

        $this->assertSame(
            'Test Calendar',
            $Model->name
        );

        $this->assertSame(
            1,
            $Model->id
        );
    }

    public function testSetupExtrasReturnsArrayOfExtras()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}',
                'foo' => 'bar',
            ],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarExtras = $CalendarIntegration->setupExtras(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra,
            $data
        );

        $this->assertSame(
            2,
            count($calendarExtras)
        );
    }

    public function testSetupExtrasReturnBlankArrayWhenNoExtrasSet()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarExtras = $CalendarIntegration->setupExtras(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\CalendarExtra,
            $data
        );

        $this->assertSame(
            0,
            count($calendarExtras)
        );
    }

    public function testSetupOptionsReturnsArrayOfOptions()
    {
        $data = [
            'id' => 1,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [
            ],
            'options' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}',
                'foo' => 'bar',
            ]
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarOptions = $CalendarIntegration->setupOptions(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Option,
            $data
        );

        $this->assertSame(
            2,
            count($calendarOptions)
        );
    }

    public function testSetupOptionsReturnBlankArrayWhenNoOptionsSet()
    {
        $data = [
            'id' => null,
            'name' => 'Test Calendar',
            'user_id' => 1,
            'extras' => [],
            'options' => []
        ];

        $CalendarIntegration = new CalendarIntegration;

        $calendarOptions = $CalendarIntegration->setupOptions(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Option,
            $data
        );
        $this->assertSame(
            0,
            count($calendarOptions)
        );
    }

    public function testSaveCalendarReturnsSuccessResultObject()
    {
        $CalendarModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
        $CalendarModel->method('save')
            ->willReturn(true);

        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->saveCalendar($CalendarModel);

        $this->assertTrue(
            $Result->isSuccess()
        );

        $this->assertSame(
            'saved',
            $Result->getCode()
        );
    }

    public function testSaveCalendarReturnsFailResultObject()
    {
        $CalendarModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
        $CalendarModel->method('save')
            ->will($this->throwException(new \Exception('Save failed')));

        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->saveCalendar($CalendarModel);

        $this->assertTrue(
            $Result->isFail()
        );

        $this->assertSame(
            'Save failed',
            $Result->getMessage()
        );
    }

    public function testSaveExtrasReturnsSuccessResultObject()
    {
        $CalendarModel = $this->buildSuccessCalendarModel('calendarExtra');

        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->saveExtras(
            $CalendarModel,
            []
        );

        $this->assertTrue(
            $Result->isSuccess()
        );

        $this->assertSame(
            'saved',
            $Result->getCode()
        );
    }

    public function testSaveExtrasReturnsFailSesultObject()
    {
        $CalendarModel = $this->buildFailCalendarModel('calendarExtra');

        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->saveExtras(
            $CalendarModel,
            []
        );

        $this->assertTrue(
            $Result->isFail()
        );

        $this->assertSame(
            'Save failed',
            $Result->getMessage()
        );
    }

    public function testSaveOptionsReturnsSuccessResultObject()
    {
        $CalendarModel = $this->buildSuccessCalendarModel('calendarOption');

        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->saveOptions(
            $CalendarModel,
            []
        );

        $this->assertTrue(
            $Result->isSuccess()
        );

        $this->assertSame(
            'saved',
            $Result->getCode()
        );
    }

    public function testSaveOptionsReturnsFailSesultObject()
    {
        $CalendarModel = $this->buildFailCalendarModel('calendarOption');

        $CalendarIntegration = new CalendarIntegration;
        $Result = $CalendarIntegration->saveOptions(
            $CalendarModel,
            []
        );

        $this->assertTrue(
            $Result->isFail()
        );

        $this->assertSame(
            'Save failed',
            $Result->getMessage()
        );
    }

    public function testLoadModelLoadsCalendarData()
    {
        $returnModel = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar')
            ->setMethods(null)
            ->getMock();
        $returnModel->setRawAttributes([
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
            ]
        ]);

        $whereMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['with', 'first'])
            ->getMock();

        $whereMock->expects($this->once())
            ->method('with')
            ->will($this->returnSelf());

        $whereMock->expects($this->once())
            ->method('first')
            ->willReturn($returnModel);

        $CalendarModel = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar')
            ->setMethods(['where'])
            ->getMock();

        $CalendarModel->expects($this->once())
            ->method('where')
            ->willReturn($whereMock);

        $CalendarIntegration = new CalendarIntegration;

        $this->assertSame(
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
                ]
            ],
            $CalendarIntegration->loadModel(
                $CalendarModel,
                10
            )
        );
    }

    public function testLoadModelReturnsEmptyArrayWhenNoRowFound()
    {
        $whereMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['with', 'first'])
            ->getMock();

        $whereMock->expects($this->once())
            ->method('with')
            ->will($this->returnSelf());

        $whereMock->expects($this->once())
            ->method('first')
            ->will($this->throwException(new \Exception('No record found')));

        $CalendarModel = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar')
            ->setMethods(['where'])
            ->getMock();

        $CalendarModel->expects($this->once())
            ->method('where')
            ->willReturn($whereMock);

        $CalendarIntegration = new CalendarIntegration;

        $loadResult = $CalendarIntegration->loadModel(
            $CalendarModel,
            10
        );

        $this->assertEmpty($loadResult);
        $this->assertSame(
            [],
            $loadResult
        );
    }

    public function testFormatExtrasFormatsCorrectly()
    {
        $CalendarIntegration = new CalendarIntegration;
        $fromDb = [
            'id' => 1,
            'name' => 'mikes cal',
            'user_id' => 1,
            'created_at' => '2016-12-28 11:40:24',
            'updated_at' => '2016-12-28 11:40:24',
            'calendar_extra' => [
                [
                    'slug' => 'author',
                    'value' => 'mike',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ],
                [
                    'slug' => 'foo',
                    'value' => 'bar',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ],
                [
                    'slug' => 'stuff',
                    'value' => 'a:3:{i:0;s:3:"foo";i:1;s:3:"bar";i:2;s:7:"barfoo1";}',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ]
            ],
            'calendar_option' => [
                [
                    'slug' => 'days',
                    'value' => 'a:7:{i:0;s:3:"Sun";i:1;s:3:"Mon";i:2;s:3:"Tue";i:3;s:3:"Wed";i:4;s:3:"Thu";i:5;s:3:"Fri";i:6;s:3:"Sat";}',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24',
                ],
                [
                    'slug' => 'defaultTimezone',
                    'value' => 'America/New_York',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ],
                [
                    'slug' => 'displayTable',
                    'value' => 'a:7:{s:10:"tableClass";s:11:"table mycal";s:7:"tableId";s:5:"MyCal";s:14:"headerRowClass";s:16:"mycal-header-row";s:11:"headerClass";s:12:"mycal-header";s:8:"rowClass";s:9:"mycal-row";s:9:"dateClass";s:10:"mycal-date";s:10:"emptyClass";s:11:"mycal-empty";}',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ],
                [
                    'slug' => 'weekStartsOn',
                    'value' => 0,
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ]
            ]
        ];

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'mikes cal',
                'user_id' => 1,
                'created_at' => '2016-12-28 11:40:24',
                'updated_at' => '2016-12-28 11:40:24',
                'calendar_option' => [
                    [
                        'slug' => 'days',
                        'value' => 'a:7:{i:0;s:3:"Sun";i:1;s:3:"Mon";i:2;s:3:"Tue";i:3;s:3:"Wed";i:4;s:3:"Thu";i:5;s:3:"Fri";i:6;s:3:"Sat";}',
                        'calendar_id' => 1,
                        'created_at' => '2016-12-28 11:40:24',
                        'updated_at' => '2016-12-28 11:40:24',
                    ],
                    [
                        'slug' => 'defaultTimezone',
                        'value' => 'America/New_York',
                        'calendar_id' => 1,
                        'created_at' => '2016-12-28 11:40:24',
                        'updated_at' => '2016-12-28 11:40:24'
                    ],
                    [
                        'slug' => 'displayTable',
                        'value' => 'a:7:{s:10:"tableClass";s:11:"table mycal";s:7:"tableId";s:5:"MyCal";s:14:"headerRowClass";s:16:"mycal-header-row";s:11:"headerClass";s:12:"mycal-header";s:8:"rowClass";s:9:"mycal-row";s:9:"dateClass";s:10:"mycal-date";s:10:"emptyClass";s:11:"mycal-empty";}',
                        'calendar_id' => 1,
                        'created_at' => '2016-12-28 11:40:24',
                        'updated_at' => '2016-12-28 11:40:24'
                    ],
                    [
                        'slug' => 'weekStartsOn',
                        'value' => 0,
                        'calendar_id' => 1,
                        'created_at' => '2016-12-28 11:40:24',
                        'updated_at' => '2016-12-28 11:40:24'
                    ]
                ],
                'extras' => [
                    'author' => 'mike',
                    'foo' => 'bar',
                    'stuff' => [
                        'foo',
                        'bar',
                        'barfoo1'
                    ]
                ]
            ],
            $CalendarIntegration->formatExtras($fromDb)
        );
    }

    public function testFormatOptionsFormatsCorrecctly()
    {
        $CalendarIntegration = new CalendarIntegration;
        $fromDb = [
            'id' => 1,
            'name' => 'mikes cal',
            'user_id' => 1,
            'created_at' => '2016-12-28 11:40:24',
            'updated_at' => '2016-12-28 11:40:24',
            'calendar_option' => [
                [
                    'slug' => 'days',
                    'value' => 'a:7:{i:0;s:3:"Sun";i:1;s:3:"Mon";i:2;s:3:"Tue";i:3;s:3:"Wed";i:4;s:3:"Thu";i:5;s:3:"Fri";i:6;s:3:"Sat";}',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24',
                ],
                [
                    'slug' => 'defaultTimezone',
                    'value' => 'America/New_York',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ],
                [
                    'slug' => 'displayTable',
                    'value' => 'a:7:{s:10:"tableClass";s:11:"table mycal";s:7:"tableId";s:5:"MyCal";s:14:"headerRowClass";s:16:"mycal-header-row";s:11:"headerClass";s:12:"mycal-header";s:8:"rowClass";s:9:"mycal-row";s:9:"dateClass";s:10:"mycal-date";s:10:"emptyClass";s:11:"mycal-empty";}',
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ],
                [
                    'slug' => 'weekStartsOn',
                    'value' => 0,
                    'calendar_id' => 1,
                    'created_at' => '2016-12-28 11:40:24',
                    'updated_at' => '2016-12-28 11:40:24'
                ]
            ],
            'extras' => [
                'author' => 'mike',
                'foo' => 'bar',
                'stuff' => [
                    'foo',
                    'bar',
                    'barfoo1'
                ]
            ]
        ];

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'mikes cal',
                'user_id' => 1,
                'created_at' => '2016-12-28 11:40:24',
                'updated_at' => '2016-12-28 11:40:24',
                'extras' => [
                    'author' => 'mike',
                    'foo' => 'bar',
                    'stuff' => [
                        'foo',
                        'bar',
                        'barfoo1'
                    ]
                ],
                'options' => [
                    'days' => [
                        'Sun',
                        'Mon',
                        'Tue',
                        'Wed',
                        'Thu',
                        'Fri',
                        'Sat'
                    ],
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
                    'weekStartsOn' => 0
                ]
            ],
            $CalendarIntegration->formatOptions($fromDb)
        );
    }

    protected function buildSuccessCalendarModel($relation)
    {
        $whereMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['delete'])
            ->getMock();

        $whereMock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $relatedMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['where'])
            ->getMock();

        $relatedMock->expects($this->once())
            ->method('where')
            ->willReturn($whereMock);

        $relationMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['saveMany', 'getRelated'])
            ->getMock();

        $relationMock->expects($this->once())
             ->method('saveMany')
             ->willReturn(true);

        $relationMock->expects($this->once())
            ->method('getRelated')
            ->willReturn($relatedMock);


        $CalendarModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
        $CalendarModel->method($relation)
            ->willReturn($relationMock);

        $CalendarModel->method('__get')
            ->with('id')
            ->willReturn(1);

        return $CalendarModel;
    }

    protected function buildFailCalendarModel($relation)
    {
        $whereMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['delete'])
            ->getMock();

        $whereMock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $relatedMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['where'])
            ->getMock();

        $relatedMock->expects($this->once())
            ->method('where')
            ->willReturn($whereMock);

        $relationMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['saveMany', 'getRelated'])
            ->getMock();

        $relationMock->expects($this->once())
             ->method('saveMany')
             ->will($this->throwException(new \Exception('Save failed')));

        $relationMock->expects($this->once())
            ->method('getRelated')
            ->willReturn($relatedMock);


        $CalendarModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
        $CalendarModel->method($relation)
            ->willReturn($relationMock);

        $CalendarModel->method('__get')
            ->with('id')
            ->willReturn(1);

        return $CalendarModel;
    }
}
