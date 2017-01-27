<?php

namespace Snscripts\MyCal\Tests\Integrations\Eloquent;

use Snscripts\MyCal\Integrations\Eloquent\Event as EventIntegration;
use Snscripts\MyCal\Calendar\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->EventInterfaceMock = $this->createMock('\Snscripts\MyCal\Interfaces\EventInterface');

        $this->eventObj = new Event(
            $this->EventInterfaceMock,
            new \DateTimeZone(
                'Europe/London'
            )
        );
    }

    public function testGetExtraDataExtractsData()
    {
        $this->eventObj->name = 'Test Event';
        $this->eventObj->calendar_id = 1;
        $this->eventObj->unixStart = 1484851680;
        $this->eventObj->unixEnd = 1484938080;
        $this->eventObj->test = [
            'foo' => 'bar',
            'foobar' => 'barfoo'
        ];

        $EventIntegration = new EventIntegration;

        $this->assertSame(
            [
                'id' => null,
                'name' => 'Test Event',
                'start_date' => 1484851680,
                'end_date' => 1484938080,
                'calendar_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ]
            ],
            $EventIntegration->getEventData(
                $this->eventObj
            )
        );

        $this->eventObj->id = 12;
        $this->assertSame(
            [
                'id' => 12,
                'name' => 'Test Event',
                'start_date' => 1484851680,
                'end_date' => 1484938080,
                'calendar_id' => 1,
                'extras' => [
                    'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
                ]
            ],
            $EventIntegration->getEventData(
                $this->eventObj
            )
        );
    }

    public function testSetupModelCreatesBlankEloquentModel()
    {
        $data = [
            'id' => null,
            'name' => 'Test Event',
            'start_date' => 1484851680,
            'end_date' => 1484938080,
            'calendar_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ]
        ];

        $EventIntegration = new EventIntegration;
        $Model = $EventIntegration->setupModel(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\Event,
            $data
        );

        $this->assertInstanceOf(
            '\Snscripts\MyCal\Integrations\Eloquent\Models\Event',
            $Model
        );

        $this->assertSame(
            'Test Event',
            $Model->name
        );
    }

    public function testSetupModelReturnsEventModel()
    {
        $data = [
            'id' => 1,
            'name' => 'Test Event',
            'start_date' => 1484851680,
            'end_date' => 1484938080,
            'calendar_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}'
            ]
        ];

        $Event = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Event')
            ->setMethods(null)
            ->getMock();
        $Event->setRawAttributes($data);

        $EventModel = $this->getMockBuilder('\Snscripts\MyCal\Integrations\Eloquent\Models\Event')
            ->setMethods(['find'])
            ->getMock();

        $EventModel->expects($this->once())
             ->method('find')
             ->willReturn($Event);

         $EventIntegration = new EventIntegration;
         $Model = $EventIntegration->setupModel(
             $EventModel,
             $data
         );

         $this->assertInstanceOf(
             '\Snscripts\MyCal\Integrations\Eloquent\Models\Event',
             $Model
         );

         $this->assertSame(
             'Test Event',
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
            'name' => 'Test Event',
            'start_date' => 1484851680,
            'end_date' => 1484938080,
            'calendar_id' => 1,
            'extras' => [
                'test' => 'a:2:{s:3:"foo";s:3:"bar";s:6:"foobar";s:6:"barfoo";}',
                'foo' => 'bar',
                'bar' => 'foo'
            ]
        ];

        $EventIntegration = new EventIntegration;

        $eventExtras = $EventIntegration->setupExtras(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\EventExtra,
            $data
        );

        $this->assertSame(
            3,
            count($eventExtras)
        );
    }

    public function testSetupExtrasReturnBlankArrayWhenNoExtrasSet()
    {
        $data = [
            'id' => null,
            'name' => 'Test Event',
            'start_date' => 1484851680,
            'end_date' => 1484938080,
            'calendar_id' => 1,
            'extras' => []
        ];

        $EventIntegration = new EventIntegration;

        $eventExtras = $EventIntegration->setupExtras(
            new \Snscripts\MyCal\Integrations\Eloquent\Models\EventExtra,
            $data
        );

        $this->assertSame(
            0,
            count($eventExtras)
        );
    }


    public function testSaveEventReturnsSuccessResultObject()
    {
        $EventModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Event');
        $EventModel->method('save')
            ->willReturn(true);

        $EventIntegration = new EventIntegration;
        $Result = $EventIntegration->saveEvent($EventModel);

        $this->assertTrue(
            $Result->isSuccess()
        );

        $this->assertSame(
            'saved',
            $Result->getCode()
        );
    }

    public function testSaveEventReturnsFailResultObject()
    {
        $EventModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Event');
        $EventModel->method('save')
            ->will($this->throwException(new \Exception('Save failed')));

        $EventIntegration = new EventIntegration;
        $Result = $EventIntegration->saveEvent($EventModel);

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
        $EventModel = $this->buildSuccessEventModel('eventExtra');

        $EventIntegration = new EventIntegration;
        $Result = $EventIntegration->saveExtras(
            $EventModel,
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
        $EventModel = $this->buildFailEventModel('eventExtra');

        $EventIntegration = new EventIntegration;
        $Result = $EventIntegration->saveExtras(
            $EventModel,
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

    protected function buildSuccessEventModel($relation)
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

            $EventModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Event');
            $EventModel->method($relation)
                ->willReturn($relationMock);

            $EventModel->method('__get')
                ->with('id')
                ->willReturn(1);

            return $EventModel;
    }

    protected function buildFailEventModel($relation)
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

        $EventModel = $this->createMock('\Snscripts\MyCal\Integrations\Eloquent\Models\Event');
        $EventModel->method($relation)
            ->willReturn($relationMock);

        $EventModel->method('__get')
            ->with('id')
            ->willReturn(1);

        return $EventModel;
    }
}
