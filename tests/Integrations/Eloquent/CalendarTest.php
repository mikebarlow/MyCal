<?php

namespace Snscripts\MyCal\Tests\Integrations\Eloquent;

use Snscripts\MyCal\Integrations\Eloquent\Calendar as CalendarIntegration;
use Snscripts\MyCal\Calendar\Calendar;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->CalendarInterfaceMock = $this->getMock('\Snscripts\MyCal\Interfaces\CalendarInterface');
        $this->DateFactoryMock = $this->getMock('\Snscripts\MyCal\DateFactory');
        $this->OptionsMock = $this->getMock('\Snscripts\MyCal\Calendar\Options');
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
        $CalendarModel = $this->getMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
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
        $CalendarModel = $this->getMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
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


        $CalendarModel = $this->getMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
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


        $CalendarModel = $this->getMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Calendar');
        $CalendarModel->method($relation)
            ->willReturn($relationMock);

        $CalendarModel->method('__get')
            ->with('id')
            ->willReturn(1);

        return $CalendarModel;
    }
}
